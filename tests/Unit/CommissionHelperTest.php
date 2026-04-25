<?php

namespace Tests\Unit;

use App\Helpers\CommissionHelper;
use App\Models\ClientCommissionOverride;
use App\Models\CommissionSetting;
use Modules\Core\Models\UnifiedUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommissionHelperTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeUser(): UnifiedUser
    {
        return UnifiedUser::create([
            'name'     => 'Test User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
        ]);
    }

    private function makeCommissionSetting(float $pct): CommissionSetting
    {
        CommissionSetting::query()->delete();

        return CommissionSetting::create([
            'commission_pct'           => $pct,
            'webmaster_pct'            => 70.00,
            'deposit_fee_pct'          => 0.00,
            'webmaster_withdrawal_pct' => 10.00,
            'performer_withdrawal_pct' => 10.00,
            'client_withdrawal_pct'    => 10.00,
            'min_withdrawal_amount'    => 10.00,
            'valid_from'               => now()->subMinute(),
            'created_by'               => 1,
        ]);
    }

    private function makeOverride(int $userId, string $role, float $pct, int $createdBy): ClientCommissionOverride
    {
        return ClientCommissionOverride::create([
            'user_id'        => $userId,
            'role'           => $role,
            'withdrawal_pct' => $pct,
            'created_by'     => $createdBy,
        ]);
    }

    private function makeSystemBalance(float $amount): void
    {
        DB::table('seolinkplace_settings')->insert([
            'key'        => 'system_balance',
            'value'      => (string)$amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ─── getWithdrawalPct ────────────────────────────────────────────────────

    public function test_returns_30_from_default_migration_seed_when_no_override(): void
    {
        // Міграція commission_settings вставляє запис із commission_pct=30
        $pct = CommissionHelper::getWithdrawalPct(999, 'client');

        $this->assertEquals(30.0, $pct);
    }

    public function test_returns_global_commission_pct_when_no_override(): void
    {
        $this->makeCommissionSetting(25.00);

        $pct = CommissionHelper::getWithdrawalPct(999, 'client');

        $this->assertEquals(25.0, $pct);
    }

    public function test_returns_override_pct_when_override_exists(): void
    {
        $this->makeCommissionSetting(25.00);
        $admin = $this->makeUser();
        $user  = $this->makeUser();
        $this->makeOverride($user->id, 'client', 10.00, $admin->id);

        $pct = CommissionHelper::getWithdrawalPct($user->id, 'client');

        $this->assertEquals(10.0, $pct);
    }

    public function test_override_is_role_specific(): void
    {
        $this->makeCommissionSetting(25.00);
        $admin = $this->makeUser();
        $user  = $this->makeUser();
        $this->makeOverride($user->id, 'webmaster', 5.00, $admin->id);

        // Запитуємо як client — override тільки для webmaster
        $pct = CommissionHelper::getWithdrawalPct($user->id, 'client');

        $this->assertEquals(25.0, $pct);
    }

    // ─── calculate ───────────────────────────────────────────────────────────

    public function test_calculate_returns_correct_breakdown(): void
    {
        $this->makeCommissionSetting(20.00);

        $result = CommissionHelper::calculate(100.00, 999, 'client');

        $this->assertEquals(20.0,  $result['pct']);
        $this->assertEquals(20.00, $result['commission']);
        $this->assertEquals(80.00, $result['net']);
    }

    public function test_calculate_uses_override_pct(): void
    {
        $this->makeCommissionSetting(30.00);
        $admin = $this->makeUser();
        $user  = $this->makeUser();
        $this->makeOverride($user->id, 'client', 10.00, $admin->id);

        $result = CommissionHelper::calculate(200.00, $user->id, 'client');

        $this->assertEquals(10.0,   $result['pct']);
        $this->assertEquals(20.00,  $result['commission']);
        $this->assertEquals(180.00, $result['net']);
    }

    public function test_calculate_rounds_to_4_decimal_places(): void
    {
        $this->makeCommissionSetting(30.00);

        $result = CommissionHelper::calculate(333.333, 999, 'client');

        $this->assertEquals(round(333.333 * 0.30, 4), $result['commission']);
        $this->assertEquals(round(333.333 * 0.70, 4), $result['net']);
    }

    // ─── addToSystemBalance ──────────────────────────────────────────────────

    public function test_add_to_system_balance_increments_value(): void
    {
        $this->makeSystemBalance(100.00);

        CommissionHelper::addToSystemBalance(50.00);

        $value = DB::table('seolinkplace_settings')
            ->where('key', 'system_balance')
            ->value('value');

        $this->assertEquals(150.00, (float)$value);
    }

    public function test_add_to_system_balance_ignores_zero(): void
    {
        $this->makeSystemBalance(100.00);

        CommissionHelper::addToSystemBalance(0);

        $value = DB::table('seolinkplace_settings')
            ->where('key', 'system_balance')
            ->value('value');

        $this->assertEquals(100.00, (float)$value);
    }

    public function test_add_to_system_balance_ignores_negative(): void
    {
        $this->makeSystemBalance(100.00);

        CommissionHelper::addToSystemBalance(-10);

        $value = DB::table('seolinkplace_settings')
            ->where('key', 'system_balance')
            ->value('value');

        $this->assertEquals(100.00, (float)$value);
    }
}
