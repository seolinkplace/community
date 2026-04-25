<?php
namespace Modules\Core\Helpers;

use App\Models\CommissionSetting;
use App\Models\ClientCommissionOverride;

class CommissionHelper
{
    public static function getWithdrawalPct(int $userId, string $role): float
    {
        $override = ClientCommissionOverride::getForUser($userId, $role);
        if ($override) {
            return (float)$override->withdrawal_pct;
        }
        return (float)(CommissionSetting::latest('valid_from')->value('commission_pct') ?? 30);
    }

    public static function calculate(float $amount, int $userId, string $role): array
    {
        $pct        = self::getWithdrawalPct($userId, $role);
        $commission = round($amount * $pct / 100, 4);
        $net        = round($amount - $commission, 4);
        return ['pct' => $pct, 'commission' => $commission, 'net' => $net];
    }

    public static function addToSystemBalance(float $commission): void
    {
        if ($commission <= 0) return;
        \Illuminate\Support\Facades\DB::table('seolinkplace_settings')
            ->where('key', 'system_balance')
            ->update([
                'value'      => \Illuminate\Support\Facades\DB::raw("value + {$commission}"),
                'updated_at' => now(),
            ]);
    }
}
