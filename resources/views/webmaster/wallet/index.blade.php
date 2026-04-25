@extends('webmaster.layouts.app')
@section('title', __('nav.wallet'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    {{-- Баланс: 3 цифри --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('wallet.total_balance') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($balance, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('wallet.frozen_balance') }}</p>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">${{ number_format($frozen, 2) }}</p>
                @if($frozen > 0)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ __('wallet.frozen_hint') }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('wallet.available_for_withdrawal') }}</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($availableForWithdrawal, 2) }}</p>
            </div>
        </div>
        @if(($wallet->pending ?? 0) > 0)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">{{ __('client.wm_pending') }} ${{ number_format($wallet->pending, 2) }}</p>
        @endif
    </div>

    {{-- Внутрішній переказ --}}
    <div class="mb-4">
        <a href="{{ route('webmaster.wallet.transfers') }}"
           class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 px-6 py-4 hover:border-gray-400 transition-colors">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.transfer_title') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('client.transfer_subtitle') }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Форма виводу --}}
    @if($wallet)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_withdraw_title') }}</h2>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
        @endif

        @if($availableForWithdrawal < 10)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400 text-sm rounded-lg px-4 py-3">
                {{ __('wallet.no_funds_available') }}
                @if($frozen > 0)
                    {{ __('wallet.frozen_unlock_hint') }}
                @endif
            </div>
        @else
        <form method="POST" action="{{ route('webmaster.wallet.withdraw') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.amount_usd') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                    <input type="number" name="amount" min="10" step="0.01" max="{{ $availableForWithdrawal }}"
                           value="{{ old('amount', max(10, min(50, floor($availableForWithdrawal)))) }}"
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
        @endif
    </div>
    @endif

    {{-- Історія нарахувань --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_accruals') }}</h2>
        @forelse($transactions as $tx)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div class="flex-1 min-w-0 mr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $tx->description ?? __('client.wm_accrual_default') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $tx->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="text-sm font-medium {{ $tx->type === 'earning' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                    {{ $tx->type === 'earning' ? '+' : '-' }}${{ number_format($tx->amount, 4) }}
                </span>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ __('client.wm_balance_after') }} ${{ number_format($tx->balance_after, 4) }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('client.wm_no_accruals') }}</p>
        @endforelse
        @if($transactions->hasPages())
        <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>

    {{-- Історія заявок --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_withdrawals') }}</h2>
        @forelse($withdrawals as $wd)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div class="flex-1 min-w-0 mr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200">
                    ${{ number_format($wd->amount, 2) }}
                    <span class="text-gray-400 dark:text-gray-500">— {{ $wd->method }}</span>
                    @if($wd->commission > 0)
                        <span class="text-xs text-yellow-500 dark:text-yellow-400 ml-1">({{ __('client.commission') }}: ${{ number_format($wd->commission, 2) }})</span>
                    @endif
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $wd->created_at->format('d.m.Y H:i') }}</p>
                @if($wd->admin_note)
                    <p class="text-xs text-red-400 dark:text-red-400 mt-0.5">{{ $wd->admin_note }}</p>
                @endif
            </div>
            <div class="flex-shrink-0">
                @php
                    $wdColors = [
                        'pending'    => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                        'processing' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                        'completed'  => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                        'rejected'   => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                    ];
                    $wdLabels = [
                        'pending'    => __('client.wd_pending'),
                        'processing' => __('client.wd_processing'),
                        'completed'  => __('client.wd_completed'),
                        'rejected'   => __('client.wd_rejected'),
                    ];
                @endphp
                <span class="text-xs px-2 py-1 rounded-full {{ $wdColors[$wd->status] ?? '' }}">
                    {{ $wdLabels[$wd->status] ?? $wd->status }}
                </span>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('client.wm_no_withdrawals') }}</p>
        @endforelse
        @if($withdrawals->hasPages())
        <div class="mt-4">{{ $withdrawals->links() }}</div>
        @endif
    </div>

</div>
@endsection
