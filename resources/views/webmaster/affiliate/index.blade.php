@extends('webmaster.layouts.app')
@section('title', __('client.affiliate_title'))
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ __('client.affiliate_title') }}</h1>

    {{-- Реферальний код --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('client.affiliate_your_code') }}</p>
        <div class="flex items-center gap-3">
            <code class="text-xl font-mono font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700">
                {{ $refCode }}
            </code>
            <button onclick="navigator.clipboard.writeText('{{ url('/u/register') }}?ref={{ $refCode }}'); this.textContent='{{ __('client.copied') }}'; setTimeout(()=>this.textContent='{{ __('client.affiliate_copy_link') }}', 2000)"
                class="text-xs border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                {{ __('client.affiliate_copy_link') }}
            </button>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            {{ __('client.affiliate_share') }}: <span class="font-mono">{{ url('/u/register') }}?ref={{ $refCode }}</span>
        </p>
    </div>

    {{-- Статистика --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.affiliate_balance') }}</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($wallet?->balance ?? 0, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.affiliate_total_earned') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($wallet?->total_earned ?? 0, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.affiliate_referrals') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $referralsCount }}</p>
        </div>
    </div>

    @if(($wallet?->balance ?? 0) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        {{-- Вивід --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.affiliate_withdraw') }}</h2>
            @if(session('success_withdrawal'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success_withdrawal') }}</div>
            @endif
            <form method="POST" action="{{ route('webmaster.affiliate.withdraw') }}">
                @csrf
                <input type="hidden" name="type" value="withdrawal">
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('client.amount_usd') }}</label>
                    <input type="number" name="amount" min="10" step="0.01" max="{{ $wallet->balance }}"
                           value="{{ max(10, min(50, floor($wallet->balance))) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('client.wm_usdt_address') }}</label>
                    <textarea name="details" rows="2" placeholder="{{ __('client.wm_usdt_address') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none"></textarea>
                </div>
                <input type="hidden" name="method" value="crypto">
                <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100 transition">
                    {{ __('client.wm_withdraw_submit') }}
                </button>
            </form>
        </div>

        {{-- Переказ --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.affiliate_transfer') }}</h2>
            @if(session('success_transfer'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success_transfer') }}</div>
            @endif
            <form method="POST" action="{{ route('webmaster.affiliate.withdraw') }}">
                @csrf
                <input type="hidden" name="type" value="transfer">
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('client.amount_usd') }}</label>
                    <input type="number" name="amount" min="1" step="0.01" max="{{ $wallet->balance }}"
                           value="{{ floor($wallet->balance) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('client.affiliate_transfer_to') }}</label>
                    <select name="transfer_to" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none">
                        @if($user->hasRole('client'))
                        <option value="client">{{ __('client.affiliate_client_wallet') }}</option>
                        @endif
                        @if($user->hasRole('webmaster'))
                        <option value="webmaster">{{ __('client.affiliate_webmaster_wallet') }}</option>
                        @endif
                    </select>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 mb-4 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('client.affiliate_transfer_instant') }}
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                    {{ __('client.affiliate_transfer_btn') }}
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Реферали --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.affiliate_referrals_list') }}</h2>
        @forelse($referrals as $ref)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div>
                <p class="text-sm text-gray-800 dark:text-gray-200">{{ $ref->referee->name ?? '—' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $ref->created_at->format('d.m.Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.affiliate_earned_from') }}</p>
                <p class="text-sm font-medium text-green-600 dark:text-green-400">
                    ${{ number_format($ref->transactions_sum_affiliate_amount ?? 0, 2) }}
                </p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('client.affiliate_no_referrals') }}</p>
        @endforelse
        @if($referrals->hasPages())
        <div class="mt-4">{{ $referrals->links() }}</div>
        @endif
    </div>

    {{-- Транзакції --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.wm_accruals') }}</h2>
        @forelse($transactions as $tx)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('client.affiliate_referral') }}: {{ $tx->referral->referee->name ?? '—' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $tx->created_at->format('d.m.Y H:i') }} · {{ $tx->pct_applied }}% {{ __('client.affiliate_from') }} ${{ $tx->commission_amount }}</p>
            </div>
            <span class="text-sm font-medium text-green-600 dark:text-green-400">+${{ number_format($tx->affiliate_amount, 2) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('client.wm_no_accruals') }}</p>
        @endforelse
        @if($transactions->hasPages())
        <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
