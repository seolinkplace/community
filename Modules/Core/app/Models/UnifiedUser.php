<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Article;
use App\Models\Campaign;
use App\Models\ClientProfile;
use App\Models\DirectPayment;
use App\Models\DirectPaymentSpend;
use App\Models\PerformerProfile;
use App\Models\Site;
use App\Models\TaskComplaint;
use App\Models\TenantToken;
use App\Models\UserAppeal;
use App\Models\UserBlock;
use App\Models\Wallet;
use App\Models\WebmasterProfile;
use App\Models\WebmasterWallet;
use App\Models\WebmasterWithdrawal;

class UnifiedUser extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \Modules\Auth\Notifications\VerifyEmail());
    }

    protected $table = 'unified_users';

    protected $fillable = [
        'name', 'email', 'password', 'status', 'locale',
        'is_trusted', 'google_id', 'google_email', 'chat_banned_at',
        'gdpr_consent_at', 'gdpr_consent_ip', 'gdpr_deleted', 'gdpr_deleted_at',
        'banned_until', 'ban_reason', 'warning_count',
        'rules_agreed_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'gdpr_consent_at' => 'datetime',
        'gdpr_deleted_at' => 'datetime',
        'gdpr_deleted'    => 'boolean',
        'email_verified_at' => 'datetime',
        'chat_banned_at'    => 'datetime',
        'password'          => 'hashed',
        'banned_until'      => 'datetime',
        'warning_count'     => 'integer',
        'rules_agreed_at'   => 'datetime',
    ];

    // ─── Roles ───────────────────────────────────────────────────────────────

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function hasRole(string $role): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles
                ->where('role', $role)
                ->where('status', 'active')
                ->isNotEmpty();
        }

        return $this->roles()
            ->where('role', $role)
            ->where('status', 'active')
            ->exists();
    }

    public function activeRoles(): array
    {
        return $this->roles()
            ->where('status', 'active')
            ->pluck('role')
            ->toArray();
    }

    public function addRole(string $role): void
    {
        UserRole::firstOrCreate(
            ['user_id' => $this->id, 'role' => $role],
            ['status' => 'active']
        );
    }

    // ─── Profiles ────────────────────────────────────────────────────────────

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class, 'user_id');
    }

    public function webmasterProfile(): HasOne
    {
        return $this->hasOne(WebmasterProfile::class, 'user_id');
    }

    public function performerProfile(): HasOne
    {
        return $this->hasOne(PerformerProfile::class, 'user_id');
    }

    // ─── Wallet ──────────────────────────────────────────────────────────────

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function webmasterWallet(): HasOne
    {
        return $this->hasOne(WebmasterWallet::class, 'user_id');
    }

    // ─── Relations (нові таблиці після міграції) ──────────────────────────────

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'user_id');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'user_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'user_id');
    }
    // ─── Compatibility accessors ─────────────────────────────────────────────

    public function getPlanAttribute(): string
    {
        return $this->clientProfile?->plan ?? 'free';
    }

    public function getCompanyNameAttribute(): ?string
    {
        return $this->clientProfile?->company_name;
    }

    public function tenantTokens(): HasMany
    {
        return $this->hasMany(TenantToken::class, 'user_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(WebmasterWithdrawal::class, 'user_id');
    }

    // ─── Ban / Complaints / Appeals ──────────────────────────────────────────

    public function complaints(): HasMany
    {
        return $this->hasMany(TaskComplaint::class, 'reporter_id');
    }

    public function complaintsAgainst(): HasMany
    {
        return $this->hasMany(TaskComplaint::class, 'reported_id');
    }

    public function blockedUsers(): HasMany
    {
        return $this->hasMany(UserBlock::class, 'blocker_id');
    }

    public function blockedByUsers(): HasMany
    {
        return $this->hasMany(UserBlock::class, 'blocked_id');
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(UserAppeal::class, 'user_id');
    }

    public function isBanned(): bool
    {
        if ($this->banned_until === null) return false;
        if ($this->banned_until->year >= 9999) return true;
        return $this->banned_until->isFuture();
    }

    public function isPermanentlyBanned(): bool
    {
        return $this->banned_until !== null && $this->banned_until->year >= 9999;
    }

    public function isBlockedBy(int $userId): bool
    {
        return UserBlock::existsBetween($this->id, $userId);
    }

    // --- Subscriptions ---

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\UserSubscription::class, 'user_id');
    }

    public function activeSubscription(string $role): ?\App\Models\UserSubscription
    {
        return $this->subscriptions()
            ->where('role', $role)
            ->whereIn('status', ['active', 'grace'])
            ->with('plan')
            ->first();
    }

    public function currentPlan(string $role): \App\Models\SubscriptionPlan
    {
        return app(\App\Services\SubscriptionService::class)->currentPlan($this, $role);
    }

    public function hasSubscriptionFeature(string $role, string $feature): bool
    {
        return app(\App\Services\SubscriptionService::class)->hasFeature($this, $role, $feature);
    }
    // ─── Direct Payments ─────────────────────────────────────────────────────

    public function directPaymentsAsSender(): HasMany
    {
        return $this->hasMany(DirectPayment::class, 'client_id');
    }

    public function directPaymentsAsReceiver(): HasMany
    {
        return $this->hasMany(DirectPayment::class, 'webmaster_id');
    }

    public function directPaymentSpends(): HasMany
    {
        return $this->hasMany(DirectPaymentSpend::class, 'client_id');
    }

}