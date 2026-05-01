<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use App\Models\PlatformSetting;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\UnifiedUser;
use Modules\Core\Models\UserSubscription;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WebmasterWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    // ─── Feature flag ─────────────────────────────────────────────────────────

    public function isEnabled(): bool
    {
        return (bool) PlatformSetting::get('subscriptions_enabled', '0');
    }

    // ─── Get current plan for user+role ──────────────────────────────────────

    public function currentPlan(UnifiedUser $user, string $role): SubscriptionPlan
    {
        if (!$this->isEnabled()) {
            return SubscriptionPlan::freeForRole($role)
                ?? new SubscriptionPlan(['slug' => "{$role}_free", 'features' => [], 'price_monthly' => 0]);
        }

        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('role', $role)
            ->whereIn('status', ['active', 'grace'])
            ->with('plan')
            ->first();

        if ($subscription && $subscription->plan) {
            return $subscription->plan;
        }

        return SubscriptionPlan::freeForRole($role)
            ?? new SubscriptionPlan(['slug' => "{$role}_free", 'features' => [], 'price_monthly' => 0]);
    }

    public function currentSubscription(UnifiedUser $user, string $role): ?UserSubscription
    {
        return UserSubscription::where('user_id', $user->id)
            ->where('role', $role)
            ->whereIn('status', ['active', 'grace'])
            ->with('plan')
            ->first();
    }

    // ─── Check feature access ─────────────────────────────────────────────────

    public function hasFeature(UnifiedUser $user, string $role, string $feature): bool
    {
        return $this->currentPlan($user, $role)->hasFeature($feature);
    }

    public function getFeature(UnifiedUser $user, string $role, string $feature, mixed $default = null): mixed
    {
        return $this->currentPlan($user, $role)->getFeature($feature, $default);
    }

    // ─── Subscribe ────────────────────────────────────────────────────────────

    public function subscribe(UnifiedUser $user, string $role, SubscriptionPlan $plan): UserSubscription
    {
        return DB::transaction(function () use ($user, $role, $plan) {
            // Скасовуємо поточну підписку якщо є
            UserSubscription::where('user_id', $user->id)
                ->where('role', $role)
                ->whereIn('status', ['active', 'grace'])
                ->update([
                    'status'       => 'cancelled',
                    'cancelled_at' => now(),
                ]);

            // Якщо план безкоштовний — просто створюємо без списання
            if ($plan->isFree()) {
                return UserSubscription::create([
                    'user_id'    => $user->id,
                    'role'       => $role,
                    'plan_id'    => $plan->id,
                    'status'     => 'active',
                    'started_at' => now(),
                    'expires_at' => null,
                ]);
            }

            // Списуємо перший місяць
            $this->chargeWallet($user, $role, $plan);

            return UserSubscription::create([
                'user_id'         => $user->id,
                'role'            => $role,
                'plan_id'         => $plan->id,
                'status'          => 'active',
                'started_at'      => now(),
                'expires_at'      => now()->addMonth(),
                'last_charged_at' => now(),
            ]);
        });
    }

    // ─── Cancel ───────────────────────────────────────────────────────────────

    public function cancel(UnifiedUser $user, string $role): void
    {
        UserSubscription::where('user_id', $user->id)
            ->where('role', $role)
            ->whereIn('status', ['active', 'grace'])
            ->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
    }

    // ─── Monthly charge (викликається з Command) ──────────────────────────────

    public function chargeAllDue(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        UserSubscription::where('status', 'active')
            ->where('expires_at', '<=', now()->addDay()) // 1 день наперед
            ->whereHas('plan', fn($q) => $q->where('price_monthly', '>', 0))
            ->with(['user', 'plan'])
            ->chunk(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $this->chargeSubscription($subscription);
                }
            });
    }

    private function chargeSubscription(UserSubscription $subscription): void
    {
        try {
            DB::transaction(function () use ($subscription) {
                $this->chargeWallet(
                    $subscription->user,
                    $subscription->role,
                    $subscription->plan
                );

                $subscription->update([
                    'status'          => 'active',
                    'expires_at'      => now()->addMonth(),
                    'last_charged_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            // Недостатньо коштів — grace period 3 дні
            $subscription->update([
                'status'     => 'grace',
                'expires_at' => now()->addDays(3),
            ]);

            Log::warning("Subscription charge failed for user {$subscription->user_id} role {$subscription->role}: {$e->getMessage()}");
        }
    }

    private function chargeWallet(UnifiedUser $user, string $role, SubscriptionPlan $plan): void
    {
        $amount = (float) $plan->price_monthly;

        if ($role === 'webmaster') {
            $wallet = WebmasterWallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->balance < $amount) {
                throw new \RuntimeException("Insufficient webmaster wallet balance");
            }
            $wallet->decrement('balance', $amount);
        } else {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->balance < $amount) {
                throw new \RuntimeException("Insufficient client wallet balance");
            }
            $wallet->decrement('balance', $amount);
        }
    }

    // ─── Expire grace subscriptions ───────────────────────────────────────────

    public function expireGrace(): void
    {
        UserSubscription::where('status', 'grace')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);
    }
}
