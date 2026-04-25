<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBlock extends Model
{
    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'complaint_id',
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'blocker_id');
    }

    public function blocked(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'blocked_id');
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(TaskComplaint::class, 'complaint_id');
    }

    /**
     * Check if two users are mutually blocked from each other.
     */
    public static function existsBetween(int $userA, int $userB): bool
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('blocker_id', $userA)->where('blocked_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('blocker_id', $userB)->where('blocked_id', $userA);
        })->exists();
    }
}
