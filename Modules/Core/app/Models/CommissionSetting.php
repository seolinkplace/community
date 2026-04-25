<?php
namespace Modules\Core\Models;
use Illuminate\Database\Eloquent\Model;
class CommissionSetting extends Model
{
    protected $fillable = [
        'commission_pct', 'webmaster_pct', 'deposit_fee_pct',
        'webmaster_withdrawal_pct', 'performer_withdrawal_pct', 'client_withdrawal_pct',
        'min_withdrawal_amount',
        'valid_from', 'created_by', 'note',
    ];
    protected $casts = [
        'commission_pct'            => 'decimal:2',
        'webmaster_pct'             => 'decimal:2',
        'deposit_fee_pct'           => 'decimal:2',
        'webmaster_withdrawal_pct'  => 'decimal:2',
        'performer_withdrawal_pct'  => 'decimal:2',
        'client_withdrawal_pct'     => 'decimal:2',
        'min_withdrawal_amount'     => 'decimal:2',
    ];
    public static function current(): self
    {
        return self::orderByDesc('valid_from')->firstOrFail();
    }

    // Зручний хелпер для отримання % виводу по ролі
    public static function withdrawalPct(string $role): float
    {
        $s = self::current();
        return match($role) {
            'webmaster'  => (float)$s->webmaster_withdrawal_pct,
            'performer'  => (float)$s->performer_withdrawal_pct,
            'client'     => (float)$s->client_withdrawal_pct,
            default      => (float)$s->commission_pct,
        };
    }
}
