@extends('client.layouts.app')
@section('title', __('subscription.title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">

    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ __('subscription.title') }}</h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Current plan --}}
    @if($current)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('subscription.current_plan') }}</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $current->plan->getLocalizedName() }}</p>
            <div class="mt-1 flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                    {{ $current->status === 'grace' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                    {{ __('subscription.' . $current->status) }}
                </span>
                @if($current->expires_at)
                    <span class="text-xs text-gray-400">
                        {{ __('subscription.expires_at') }}: {{ $current->expires_at->format('d.m.Y') }}
                    </span>
                @endif
            </div>
            @if($current->status === 'grace')
                <p class="mt-2 text-xs text-yellow-600 dark:text-yellow-400">
                    {{ __('subscription.grace_warning', ['days' => $current->daysUntilExpiry()]) }}
                </p>
            @endif
            @if(!$current->plan->isFree())
                <div x-data="{ open: false }" class="mt-3">
                    <button type="button" @click="open = true" class="text-xs text-red-500 hover:text-red-700 underline">
                        {{ __('subscription.cancel') }}
                    </button>
                    <div x-show="open" x-cloak class="mt-2 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <p class="text-xs text-red-700 dark:text-red-400 mb-2">{{ __('subscription.confirm_cancel') }}</p>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('client.subscription.cancel') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-medium">
                                    {{ __('subscription.cancel') }}
                                </button>
                            </form>
                            <button type="button" @click="open = false" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium">
                                {{ __('common.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Plans grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($plans as $plan)
            @php $isCurrent = $current && $current->plan_id === $plan->id; @endphp
            <div class="bg-white dark:bg-gray-900 rounded-xl border {{ $isCurrent ? 'border-yellow-400 dark:border-yellow-500' : 'border-gray-200 dark:border-gray-700' }} p-5 flex flex-col">
                <div class="mb-3">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $plan->getLocalizedName() }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $plan->getLocalizedDescription() }}</p>
                </div>
                <div class="mb-4">
                    @if($plan->isFree())
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('subscription.free') }}</span>
                    @else
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="text-xs text-gray-400">{{ __('subscription.per_month') }}</span>
                    @endif
                </div>

                @if($plan->features)
                    <ul class="space-y-1 mb-4 flex-1">
                        @if(isset($plan->features['commission_pct']))
                            <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ __('subscription.features.commission_pct', ['value' => $plan->features['commission_pct']]) }}
                            </li>
                        @endif
                        @if(isset($plan->features['direct_payments']))
                            <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                @if($plan->features['direct_payments'])
                                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                {{ __('subscription.features.direct_payments') }}
                            </li>
                        @endif
                        @if(isset($plan->features['workspace']))
                            <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                @if($plan->features['workspace'])
                                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                {{ __('subscription.features.workspace') }}
                            </li>
                        @endif
                    </ul>
                @endif

                @if($isCurrent)
                    <span class="mt-auto inline-flex justify-center items-center rounded-lg border border-yellow-400 dark:border-yellow-500 px-4 py-2 text-sm font-medium text-yellow-600 dark:text-yellow-400">
                        {{ __('subscription.current_plan') }}
                    </span>
                @elseif(!$plan->is_purchasable)
                    <span class="mt-auto inline-flex justify-center items-center rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                        {{ __('common.unavailable') }}
                    </span>
                @else
                    <div x-data="{ open: false }" class="mt-auto">
                        <button type="button" @click="open = !open"
                            class="w-full inline-flex justify-center items-center rounded-lg bg-yellow-400 hover:bg-yellow-500 px-4 py-2 text-sm font-medium text-gray-900 transition-colors">
                            {{ __('subscription.choose_plan') }}
                        </button>
                        <div x-show="open" x-cloak class="mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-600 dark:text-gray-300 mb-2">
                                {{ __('subscription.confirm_subscribe', ['plan' => $plan->getLocalizedName(), 'price' => number_format($plan->price_monthly, 0)]) }}
                            </p>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('client.subscription.subscribe', $plan) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-gray-900 text-xs font-medium">
                                        {{ __('common.confirm') }}
                                    </button>
                                </form>
                                <button type="button" @click="open = false" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium">
                                    {{ __('common.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</div>
@endsection
