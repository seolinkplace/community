<?php

namespace Tests\Unit;

use Modules\Affiliate\Models\AffiliateTransaction;
use Modules\Affiliate\Models\AffiliateWallet;
use App\Models\CommissionSetting;
use Modules\Affiliate\Models\Referral;
use Modules\Affiliate\Models\ReferralProgram;
use Modules\Affiliate\Models\ReferralProgramOverride;
use Modules\Core\Models\UnifiedUser;
use Modules\Affiliate\Services\AffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AffiliateServiceTest extends TestCase
{
    use RefreshDatabase;

    private AffiliateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AffiliateService();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeUser(): UnifiedUser
    {
        return UnifiedUser::create([
            'name'     => 'User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
        ]);
    }

    private function makeCommissionSetting(float $commissionPct, float $depositFeePct = 0): CommissionSetting
    {
        CommissionSetting::query()->delete();

        return CommissionSetting::create([
            'commission_pct'           => $commissionPct,
            'webmaster_pct'            => 70.00,
            'deposit_fee_pct'          => $depositFeePct,
            'webmaster_withdrawal_pct' => 10.00,
            'performer_withdrawal_pct' => 10.00,
            'client_withdrawal_pct'    => 10.00,
            'min_withdrawal_amount'    => 10.00,
            'valid_from'               => now()->subMinute(),
            'created_by'               => 1,
        ]);
    }

    private function makeReferral(UnifiedUser $referrer, UnifiedUser $referee, ?int $createdDaysAgo = null): Referral
    {
        $data = [
            'referrer_id' => $referrer->id,
            'referee_id'  => $referee->id,
            'code'        => 'TESTCODE',
        ];

        if ($createdDaysAgo !== null) {
            $data['created_at'] = now()->subDays($createdDaysAgo);
            $data['updated_at'] = now()->subDays($createdDaysAgo);
        }

        return Referral::create($data);
    }

    private function makeProgram(float $pct, ?int $durationDays = null): ReferralProgram
    {
        ReferralProgram::query()->delete();

        return ReferralProgram::create([
            'referral_pct'  => $pct,
            'duration_days' => $durationDays,
            'valid_from'    => now()->subMinute(),
            'created_by'    => 1,
        ]);
    }

    private function setupScenario(
        float $commissionPct,
        float $referralPct,
        float $depositFeePct = 0,
        ?int  $referralAgeDays = null,
        ?int  $programDurationDays = null
    ): array {
        $referrer = $this->makeUser();
        $referee  = $this->makeUser();
        $source   = $this->makeUser();

        $this->makeReferral($referrer, $referee, $referralAgeDays);
        $this->makeCommissionSetting($commissionPct, $depositFeePct);
        $this->makeProgram($referralPct, $programDurationDays);

        return [$referrer, $referee, $source];
    }

    // ─── generateCode ────────────────────────────────────────────────────────

    public function test_generate_code_is_deterministic(): void
    {
        $user = $this->makeUser();

        $this->assertEquals(
            $this->service->generateCode($user),
            $this->service->generateCode($user)
        );
    }

    public function test_generate_code_is_8_chars_uppercase(): void
    {
        $user = $this->makeUser();
        $code = $this->service->generateCode($user);

        $this->assertEquals(8, strlen($code));
        $this->assertEquals(strtoupper($code), $code);
    }

    public function test_generate_code_differs_between_users(): void
    {
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();

        $this->assertNotEquals(
            $this->service->generateCode($user1),
            $this->service->generateCode($user2)
        );
    }

    // ─── registerReferral ────────────────────────────────────────────────────

    public function test_register_referral_creates_referral_record(): void
    {
        $referrer = $this->makeUser();
        $referee  = $this->makeUser();
        $code     = $this->service->generateCode($referrer);

        $this->service->registerReferral($referee, $code);

        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
            'referee_id'  => $referee->id,
            'code'        => strtoupper($code),
        ]);
    }

    public function test_register_referral_ignores_invalid_code(): void
    {
        $referee = $this->makeUser();

        $this->service->registerReferral($referee, 'INVALID1');

        $this->assertDatabaseCount('referrals', 0);
    }

    public function test_register_referral_ignores_self_referral(): void
    {
        $user = $this->makeUser();
        $code = $this->service->generateCode($user);

        $this->service->registerReferral($user, $code);

        $this->assertDatabaseCount('referrals', 0);
    }

    public function test_register_referral_does_not_duplicate(): void
    {
        $referrer = $this->makeUser();
        $referee  = $this->makeUser();
        $code     = $this->service->generateCode($referrer);

        $this->service->registerReferral($referee, $code);
        $this->service->registerReferral($referee, $code);

        $this->assertDatabaseCount('referrals', 1);
    }

    // ─── processCommission ───────────────────────────────────────────────────

    public function test_process_commission_credits_affiliate_wallet(): void
    {
        // grossAmount=100, commission=30%, affiliateAmount=30*20%=6
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 20
        );

        $this->service->processCommission($source, 100.00, $referee->id);

        $wallet = AffiliateWallet::where('user_id', $referrer->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(6.00, (float)$wallet->balance);
        $this->assertEquals(6.00, (float)$wallet->total_earned);
    }

    public function test_process_commission_creates_transaction_record(): void
    {
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 20
        );

        $this->service->processCommission($source, 100.00, $referee->id);

        $this->assertDatabaseHas('affiliate_transactions', [
            'gross_amount'      => 100.00,
            'commission_amount' => 30.00,
            'affiliate_amount'  => 6.00,
            'pct_applied'       => 20.00,
            'status'            => 'completed',
        ]);
    }

    public function test_process_commission_skips_when_no_referral(): void
    {
        $orphan = $this->makeUser();
        $source = $this->makeUser();
        $this->makeCommissionSetting(30);

        $this->service->processCommission($source, 100.00, $orphan->id);

        $this->assertDatabaseCount('affiliate_transactions', 0);
    }

    public function test_process_commission_skips_when_no_program(): void
    {
        $referrer = $this->makeUser();
        $referee  = $this->makeUser();
        $source   = $this->makeUser();

        $this->makeReferral($referrer, $referee);
        $this->makeCommissionSetting(30);
        ReferralProgram::query()->delete();

        $this->service->processCommission($source, 100.00, $referee->id);

        $this->assertDatabaseCount('affiliate_transactions', 0);
    }

    public function test_process_commission_uses_personal_override_over_global_program(): void
    {
        $referrer = $this->makeUser();
        $referee  = $this->makeUser();
        $source   = $this->makeUser();

        $this->makeReferral($referrer, $referee);
        $this->makeCommissionSetting(30);
        $this->makeProgram(10);

        ReferralProgramOverride::create([
            'user_id'       => $referrer->id,
            'referral_pct'  => 50.00,
            'valid_from'    => now()->subDay(),
            'valid_to'      => null,
            'grandfathered' => false,
            'created_by'    => 1,
        ]);

        $this->service->processCommission($source, 100.00, $referee->id);

        // commission=30, affiliate=30*50%=15
        $this->assertDatabaseHas('affiliate_transactions', [
            'affiliate_amount' => 15.00,
            'pct_applied'      => 50.00,
        ]);
    }

    public function test_process_commission_skips_when_referral_link_expired(): void
    {
        // Реферальний зв'язок 100 днів тому, програма діє 30 днів
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 20,
            referralAgeDays: 100,
            programDurationDays: 30
        );

        $this->service->processCommission($source, 100.00, $referee->id);

        $this->assertDatabaseCount('affiliate_transactions', 0);
    }

    public function test_process_commission_accumulates_on_existing_wallet(): void
    {
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 20
        );

        $this->service->processCommission($source, 100.00, $referee->id);
        $this->service->processCommission($source, 100.00, $referee->id);

        $wallet = AffiliateWallet::where('user_id', $referrer->id)->first();
        $this->assertEquals(12.00, (float)$wallet->balance);
        $this->assertEquals(12.00, (float)$wallet->total_earned);
    }

    // ─── processDepositCommission ────────────────────────────────────────────

    public function test_process_deposit_commission_credits_wallet(): void
    {
        // depositAmount=1000, depositFee=2%=20, affiliateAmount=20*25%=5
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 25,
            depositFeePct: 2
        );

        $this->service->processDepositCommission($source, 1000.00, $referee->id);

        $wallet = AffiliateWallet::where('user_id', $referrer->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(5.00, (float)$wallet->balance);
    }

    public function test_process_deposit_commission_skips_when_deposit_fee_is_zero(): void
    {
        [$referrer, $referee, $source] = $this->setupScenario(
            commissionPct: 30,
            referralPct: 25,
            depositFeePct: 0
        );

        $this->service->processDepositCommission($source, 1000.00, $referee->id);

        $this->assertDatabaseCount('affiliate_transactions', 0);
    }

    public function test_process_deposit_commission_skips_when_no_referral(): void
    {
        $orphan = $this->makeUser();
        $source = $this->makeUser();
        $this->makeCommissionSetting(30, 2);

        $this->service->processDepositCommission($source, 1000.00, $orphan->id);

        $this->assertDatabaseCount('affiliate_transactions', 0);
    }
}
