<?php

namespace Modules\Core\Models;

use Modules\Core\Models\UserSubscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'slug', 'role', 'name', 'description',
        'price_monthly', 'features', 'is_active', 'is_purchasable', 'sort_order',
    ];

    protected $casts = [
        'name'           => 'array',
        'description'    => 'array',
        'features'       => 'array',
        'price_monthly'  => 'decimal:2',
        'is_active'      => 'boolean',
        'is_purchasable' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? $this->slug;
    }

    public function getLocalizedDescription(): string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? '';
    }

    public function isFree(): bool
    {
        return $this->price_monthly == 0;
    }

    public function hasFeature(string $key): bool
    {
        return (bool) ($this->features[$key] ?? false);
    }

    public function getFeature(string $key, mixed $default = null): mixed
    {
        return $this->features[$key] ?? $default;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ─── Static helpers ──────────────────────────────────────────────────────

    public static function freeForRole(string $role): ?self
    {
        return static::active()
            ->forRole($role)
            ->where('price_monthly', 0)
            ->orderBy('sort_order')
            ->first();
    }
}
