<?php

namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;
use Modules\Core\Models\SubscriptionPlan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id', 'role', 'plan_id', 'status',
        'started_at', 'expires_at', 'cancelled_at', 'last_charged_at',
    ];

    protected $casts = [
        'started_at'      => 'datetime',
        'expires_at'      => 'datetime',
        'cancelled_at'    => 'datetime',
        'last_charged_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'grace']);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function daysUntilExpiry(): int
    {
        if ($this->expires_at === null) return 0;
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }
}
