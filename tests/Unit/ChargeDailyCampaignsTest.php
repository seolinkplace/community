<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Models\UnifiedUser;
use Modules\Core\Models\UserRole;
use Modules\Core\Models\Client;
use Modules\Campaigns\Models\Campaign;
use Modules\Campaigns\Models\CampaignLink;
use Modules\Sites\Models\Site;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletTransaction;
use Modules\Wallet\Models\WebmasterWallet;
use Modules\Wallet\Models\WebmasterTransaction;
use App\Models\CommissionSetting;

class ChargeDailyCampaignsTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeUnifiedUser(string $role = 'client'): UnifiedUser
    {
        $user = UnifiedUser::create([
            'name'     => 'User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
            'locale'   => 'uk',
        ]);
        UserRole::create(['user_id' => $user->id, 'role' => $role, 'status' => 'active']);
        return $user;
    }

    private function makeClient(): array
    {
        $user   = $this->makeUnifiedUser('client');
        $client = Client::create([
            'name'   => 'Client',
            'plan'   => 'starter',
            'status' => 'active',
        ]);
        return [$user, $client];
    }

    private function makeWallet(UnifiedUser $user, Client $client, float $balance): Wallet
    {
        // ClientObserver creates wallet automatically on Client::create
        $wallet = Wallet::where('client_id', $client->id)->first()
            ?? Wallet::create([
                'user_id'   => $user->id,
                'client_id' => $client->id,
                'balance'   => 0,
                'reserved'  => 0,
            ]);
        $wallet->update(['balance' => $balance]);
        return $wallet->fresh();
    }

    private function makeSite(UnifiedUser $webmaster): Site
    {
        // Command uses webmaster_wallets.webmaster_id → must match a webmasters.id
        // We insert directly to bypass the FK by using user_id as both keys
        \Illuminate\Support\Facades\DB::table('webmasters')->insertOrIgnore([
            'id'         => $webmaster->id,
            'name'       => $webmaster->name,
            'status'     => 'verified',
            'plan'       => 'free',
            'freeze_disabled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        WebmasterWallet::firstOrCreate(
            ['webmaster_id' => $webmaster->id],
            ['user_id' => $webmaster->id, 'balance' => 0, 'pending' => 0]
        );

        return Site::create([
            'user_id'              => $webmaster->id,
            'webmaster_id'         => $webmaster->id,
            'domain'               => fake()->unique()->domainName(),
            'platform_type'        => 'wordpress',
            'status'               => 'active',
            'visibility'           => 'public',
            'content_type'         => 'article',
            'is_adult'             => false,
            'pages_count'          => 0,
            'followers_count'      => 0,
            'first_post_published' => false,
            'first_post_required'  => false,
        ]);
    }

    private function makeCampaign(UnifiedUser $user, Client $client): Campaign
    {
        return Campaign::create([
            'user_id'   => $user->id,
            'client_id' => $client->id,
            'name'      => 'Test Campaign',
            'status'    => 'active',
        ]);
    }

    private function makeLink(Campaign $campaign, Site $site, float $pricePerDay, string $status = 'active', ?string $lastChargedAt = null): CampaignLink
    {
        return CampaignLink::create([
            'campaign_id'     => $campaign->id,
            'site_id'         => $site->id,
            'target_url'      => 'https://example.com',
            'link_type'       => 'dofollow',
            'placement_type'  => 'link',
            'order_type'      => 'place_only',
            'price_per_day'   => $pricePerDay,
            'status'          => $status,
            'last_charged_at' => $lastChargedAt,
            'clicks_count'    => 0,
            'clicks_billed'   => 0,
        ]);
    }

    private function makeCommissionSetting(): void
    {
        CommissionSetting::create([
            'commission_pct'           => 30,
            'webmaster_pct'            => 70,
            'deposit_fee_pct'          => 0,
            'client_withdrawal_pct'    => 10,
            'performer_withdrawal_pct' => 10,
            'webmaster_withdrawal_pct' => 10,
            'min_withdrawal_amount'    => 10,
            'valid_from'               => now()->subMinute(),
            'created_by'               => 1,
        ]);
    }

    // ─── Tests ───────────────────────────────────────────────────────────────

    public function test_charges_active_link_and_deducts_from_wallet(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $wallet          = $this->makeWallet($user, $client, 100.00);
        $site            = $this->makeSite($webmaster);
        $campaign        = $this->makeCampaign($user, $client);
        $this->makeLink($campaign, $site, 10.00);

        Artisan::call('campaigns:charge-daily');
        $artisanOutput = Artisan::output();

        $this->assertEquals(90.00, (float) $wallet->fresh()->balance, "Command output: " . $artisanOutput);
    }

    public function test_creates_wallet_transaction_on_charge(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $wallet          = $this->makeWallet($user, $client, 100.00);
        $site            = $this->makeSite($webmaster);
        $campaign        = $this->makeCampaign($user, $client);
        $this->makeLink($campaign, $site, 10.00);

        Artisan::call('campaigns:charge-daily');

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type'      => 'charge',
            'amount'    => -10.00,
            'status'    => 'completed',
        ]);
    }

    public function test_credits_webmaster_wallet_on_charge(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $this->makeWallet($user, $client, 100.00);
        $site     = $this->makeSite($webmaster);
        $campaign = $this->makeCampaign($user, $client);
        $this->makeLink($campaign, $site, 10.00);

        Artisan::call('campaigns:charge-daily');

        $wmWallet = WebmasterWallet::where('user_id', $webmaster->id)->first();
        $this->assertNotNull($wmWallet);
        $this->assertEquals(10.00, (float) $wmWallet->balance);
    }

    public function test_idempotent_does_not_charge_twice_same_day(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $wallet          = $this->makeWallet($user, $client, 100.00);
        $site            = $this->makeSite($webmaster);
        $campaign        = $this->makeCampaign($user, $client);
        $this->makeLink($campaign, $site, 10.00, 'active', now()->toDateString());

        Artisan::call('campaigns:charge-daily');

        $this->assertEquals(100.00, (float) $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_pauses_link_when_insufficient_balance(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $this->makeWallet($user, $client, 5.00);
        $site     = $this->makeSite($webmaster);
        $campaign = $this->makeCampaign($user, $client);
        $link     = $this->makeLink($campaign, $site, 10.00);

        Artisan::call('campaigns:charge-daily');

        $this->assertEquals('paused', $link->fresh()->status);
    }

    public function test_does_not_charge_paused_link(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $wallet          = $this->makeWallet($user, $client, 100.00);
        $site            = $this->makeSite($webmaster);
        $campaign        = $this->makeCampaign($user, $client);
        $this->makeLink($campaign, $site, 10.00, 'paused');

        Artisan::call('campaigns:charge-daily');

        $this->assertEquals(100.00, (float) $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_updates_last_charged_at_after_charge(): void
    {
        $this->makeCommissionSetting();
        [$user, $client] = $this->makeClient();
        $webmaster       = $this->makeUnifiedUser('webmaster');
        $this->makeWallet($user, $client, 100.00);
        $site     = $this->makeSite($webmaster);
        $campaign = $this->makeCampaign($user, $client);
        $link     = $this->makeLink($campaign, $site, 10.00);

        Artisan::call('campaigns:charge-daily');

        $this->assertEquals(now()->toDateString(), $link->fresh()->last_charged_at);
    }
}
