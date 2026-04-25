@extends('performer.layouts.app')
@section('title', __('nav.wallet'))
@section('content')
<div class="max-w-2xl mx-auto py-6">

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('client.wm_available') }}</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($wallet?->balance ?? 0, 2) }}</p>
        @if(($wallet?->pending ?? 0) > 0)
        <p class="text-xs text-gray-400 mt-1">{{ __('client.wm_pending') }} ${{ number_format($wallet->pending, 2) }}</p>
        @endif
    </div>

    @if($wallet)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_withdraw_title') }}</h2>
        <form method="POST" action="{{ route('performer.wallet.withdraw') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.amount_usd') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                    <input type="number" name="amount" min="10" step="0.01" max="{{ $wallet->balance }}"
                           value="{{ old('amount', max(10, min(50, floor($wallet->balance)))) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if($commissionPct > 0)
                <div class="mt-2 text-xs text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg px-3 py-2">
                    {{ __('client.withdrawal_commission_hint', ['pct' => $commissionPct]) }}
                </div>
                @endif
            </div>
            <input type="hidden" name="method" value="crypto">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.wm_usdt_address') }}</label>
                <textarea name="details" rows="3" placeholder="{{ __('client.wm_usdt_address') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none">{{ old('details') }}</textarea>
                @error('details')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mb-4 text-xs text-gray-500 dark:text-gray-400">
                {{ __('client.wm_withdraw_note') }}
            </div>
            <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100">
                {{ __('client.wm_withdraw_submit') }}
            </button>
        </form>
    </div>
    @endif

    @if($withdrawals->isNotEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_withdrawals') }}</h2>
        <div class="space-y-2">
            @foreach($withdrawals as $w)
            @php
                $sc = ['pending'=>'text-yellow-600 dark:text-yellow-400','approved'=>'text-green-600 dark:text-green-400','rejected'=>'text-red-600 dark:text-red-400'];
                $sl = ['pending'=>__('client.status_pending'),'approved'=>__('client.status_approved'),'rejected'=>__('client.status_rejected')];
            @endphp
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($w->amount, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ $w->method }} &middot; {{ $w->created_at->format('d.m.Y') }}</p>
                </div>
                <span class="text-xs font-semibold {{ $sc[$w->status] ?? '' }}">{{ $sl[$w->status] ?? $w->status }}</span>
            </div>
            @endforeach
        </div>
        @if($withdrawals->hasPages())
        <div class="mt-3">{{ $withdrawals->links() }}</div>
        @endif
    </div>
    @endif

</div>
@endsection
