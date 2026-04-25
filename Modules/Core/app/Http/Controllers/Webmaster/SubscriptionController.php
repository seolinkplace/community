<?php

namespace Modules\Core\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Modules\Core\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user('unified');

        $plans = SubscriptionPlan::active()
            ->forRole('webmaster')
            ->orderBy('sort_order')
            ->get();

        $current = $this->subscriptionService->currentSubscription($user, 'webmaster');

        return view('webmaster.subscription.index', compact('plans', 'current'));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = $request->user('unified');

        if (!$this->subscriptionService->isEnabled()) {
            return back()->with('error', __('subscription.subscriptions_disabled'));
        }

        if ($plan->role !== 'webmaster' || !$plan->is_active || !$plan->is_purchasable) {
            return back()->with('error', __('common.error'));
        }

        $wallet = $user->webmasterWallet;
        if (!$plan->isFree() && (!$wallet || $wallet->balance < $plan->price_monthly)) {
            return back()->with('error', __('subscription.insufficient_balance'));
        }

        $this->subscriptionService->subscribe($user, 'webmaster', $plan);

        return back()->with('success', __('subscription.subscribed', ['plan' => $plan->getLocalizedName()]));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user('unified');

        $this->subscriptionService->cancel($user, 'webmaster');

        return back()->with('success', __('subscription.cancelled'));
    }
}
