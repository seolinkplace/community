<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ClientCommissionOverride extends Model
{
    protected $table = 'user_commission_overrides';
    protected $fillable = ['user_id', 'role', 'withdrawal_pct', 'note', 'created_by'];
    protected $casts = ['withdrawal_pct' => 'decimal:2'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }
    public static function getForUser(int $userId, string $role): ?self
    {
        return self::where('user_id', $userId)->where('role', $role)->first();
    }

    // Повертає фінальний % виводу для юзера — override або дефолт по ролі
    public static function resolveWithdrawalPct(int $userId, string $role): float
    {
        $override = self::getForUser($userId, $role);
        return $override
            ? (float)$override->withdrawal_pct
            : CommissionSetting::withdrawalPct($role);
    }
}
