@extends('client.layouts.app')
@section('title', __('client.wallet_title'))

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    {{-- Баланс --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('client.current_balance') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($wallet->balance, 2) }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('client.usd_balance') }}</p>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if(request('payment') === 'success')
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm">
            {{ __('client.payment_confirmed') }}
        </div>
    @endif

    {{-- Внутрішній переказ --}}
    <div class="mb-4">
        <a href="{{ route('client.wallet.transfers') }}"
           class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 px-6 py-4 hover:border-gray-400 transition-colors">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.transfer_title') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('client.transfer_subtitle') }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Вивід залишку --}}
    @if(($wallet->balance ?? 0) >= $minAmount)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('client.client_withdraw_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('client.client_withdraw_desc') }}</p>
        <form method="POST" action="{{ route('client.wallet.withdraw') }}" x-data="{ amount: {{ number_format($wallet->balance, 2, '.', '') }}, pct: {{ $commissionPct }} }" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    {{ __('client.client_withdraw_amount') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                    <input type="number" name="amount" x-model="amount"
                           min="{{ $minAmount }}" max="{{ $wallet->balance }}" step="0.01" required
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ __('client.client_withdraw_hint', ['min' => $minAmount]) }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    {{ __('client.client_withdraw_wallet') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="details" required minlength="10"
                       placeholder="{{ __('client.client_withdraw_wallet_ph') }}"
                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
            </div>
            {{-- Калькулятор --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-xs space-y-1">
                <div class="flex justify-between text-gray-500 dark:text-gray-400">
                    <span>{{ __('client.client_withdraw_commission', ['pct' => $commissionPct]) }}</span>
                    <span x-text="'$' + (amount * pct / 100).toFixed(2)"></span>
                </div>
                <div class="flex justify-between font-semibold text-gray-900 dark:text-white">
                    <span>{{ __('client.client_withdraw_net') }}</span>
                    <span x-text="'$' + (amount - amount * pct / 100).toFixed(2)"></span>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 py-2.5 rounded-lg text-sm font-medium transition-colors">
                {{ __('client.client_withdraw_btn') }}
            </button>
        </form>
    </div>
    @endif

    {{-- Поповнення --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('client.topup_balance') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('client.topup_desc') }}</p>

        {{-- Вибір методу --}}
        <div class="flex gap-2 mb-6">
            <button onclick="switchMethod('direct')"
                    id="tab_direct"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium border-2 border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900 transition-colors">
                {{ __('client.direct_usdt') }}
            </button>
            <button onclick="switchMethod('nowpayments')"
                    id="tab_nowpayments"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium border-2 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 transition-colors hover:border-gray-400">
                {{ __('client.via_nowpayments') }}
            </button>
        </div>

        {{-- Direct USDT --}}
        <div id="method_direct">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4 text-sm">
                <p class="font-medium text-blue-800 dark:text-blue-300 mb-2">{{ __('client.how_it_works') }}</p>
                <ul class="space-y-1 text-blue-700 dark:text-blue-400">
                    <li>{{ __('client.direct_step1') }}</li>
                    <li>{{ __('client.direct_step2') }}</li>
                    <li>{{ __('client.direct_step3') }}</li>
                    <li>{{ __('client.direct_step4') }}</li>
                </ul>
                <p class="mt-2 text-blue-600 dark:text-blue-500 text-xs">
                    {!! __('client.binance_note') !!}
                </p>
            </div>

            <form method="POST" action="{{ route('client.wallet.deposit') }}">
                @csrf
                <input type="hidden" name="gateway" value="direct_usdt">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.amount_usd') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                            <input type="number" name="amount" min="5" step="0.01"
                                   value="{{ old('amount', 10) }}"
                                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                   placeholder="10.00">
                        </div>
                        <div class="flex gap-2 mt-2">
                            @foreach([10, 25, 50, 100] as $preset)
                            <button type="button"
                                onclick="document.querySelector('[name=amount]').value = {{ $preset }}"
                                class="flex-1 border border-gray-300 dark:border-gray-700 rounded-lg py-1 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                                ${{ $preset }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            {{ __('client.your_wallet') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="from_wallet"
                               placeholder="{{ __('client.wallet_placeholder') }}"
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                               @if($savedWallets->count()) list="saved_wallets" @endif>
                        @if($savedWallets->count())
                        <datalist id="saved_wallets">
                            @foreach($savedWallets as $sw)
                            <option value="{{ $sw->address }}">{{ $sw->label ?? $sw->address }}</option>
                            @endforeach
                        </datalist>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ __('client.wallet_required') }}</p>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    {{ __('client.get_requisites') }}
                </button>
            </form>
        </div>

        {{-- NOWPayments --}}
        <div id="method_nowpayments" class="hidden">
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-4 text-sm">
                <p class="font-medium text-amber-800 dark:text-amber-300 mb-2">{{ __('client.how_it_works') }}</p>
                <ul class="space-y-1 text-amber-700 dark:text-amber-400">
                    <li>→ Підтримує Binance Pay, будь-яку крипту</li>
                    <li>→ Автоматична конвертація в USD</li>
                    <li>→ Комісія NOWPayments ~0.5%</li>
                    <li>→ Мінімум $20</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('client.wallet.deposit') }}">
                @csrf
                <input type="hidden" name="gateway" value="nowpayments">

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.amount_usd') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                        <input type="number" name="amount" min="20" step="0.01"
                               value="{{ old('amount', 20) }}"
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                               placeholder="20.00">
                    </div>
                    <div class="flex gap-2 mt-2">
                        @foreach([20, 50, 100, 200] as $preset)
                        <button type="button"
                            onclick="this.closest('form').querySelector('[name=amount]').value = {{ $preset }}"
                            class="flex-1 border border-gray-300 dark:border-gray-700 rounded-lg py-1 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                            ${{ $preset }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    {{ __('client.go_nowpayments') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Історія транзакцій --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.tx_history') }}</h2>

        @forelse($transactions as $tx)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div>
                <p class="text-sm text-gray-800 dark:text-gray-200">
                    @if($tx->type === 'deposit') {{ __('client.tx_deposit') }}
                    @elseif($tx->type === 'charge') {{ __('client.tx_charge') }}
                    @elseif($tx->type === 'refund') {{ __('client.tx_refund') }}
                    @elseif($tx->type === 'admin_grant') {{ __('client.tx_admin_grant') }}
                    @elseif($tx->type === 'withdrawal') {{ __('client.tx_withdrawal') }}
                    @else {{ $tx->type }}
                    @endif
                    @if($tx->gateway && $tx->type === 'deposit')
                        <span class="text-xs text-gray-400 ml-1">
                            via {{ $tx->gateway === 'direct_usdt' ? 'USDT TRC20' : 'NOWPayments' }}
                        </span>
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $tx->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium {{ in_array($tx->type, ['deposit','refund','admin_grant','webmaster_grant']) ? 'text-green-600' : 'text-red-500' }}">
                    {{ in_array($tx->type, ['deposit','refund','admin_grant','webmaster_grant']) ? '+' : '-' }}${{ number_format(abs($tx->amount), 2) }}
                </span>
                <p class="text-xs mt-0.5">
                    @if($tx->type === 'charge')
                        <span class="text-gray-400 dark:text-gray-500">{{ __('client.tx_charge') }}</span>
                    @elseif($tx->status === 'completed')
                        <span class="text-green-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('client.tx_status_completed') }}
                        </span>
                    @elseif($tx->status === 'pending')
                        <span class="text-yellow-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('client.tx_status_pending') }}
                        </span>
                        @if($tx->gateway === 'direct_usdt' && $tx->external_id)
                            <a href="{{ route('client.wallet.pay', ['tx' => $tx->external_id]) }}"
                               class="ml-2 text-blue-500 hover:underline">{{ __('client.tx_details') }}</a>
                            <form method="POST" action="{{ route('client.wallet.cancel', $tx) }}" class="inline ml-2"
                                  onsubmit="return confirm(__('client.tx_cancel_confirm'))">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600">{{ __('client.tx_cancel') }}</button>
                            </form>
                        @endif
                    @elseif($tx->status === 'expired')
                        <span class="text-gray-400">{{ __('client.tx_status_expired') }}</span>
                    @else
                        <span class="text-gray-400">{{ $tx->status }}</span>
                    @endif
                </p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">{{ __('client.no_tx_yet') }}</p>
        @endforelse

        @if($transactions->hasPages())
        <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>

<script>
function switchMethod(method) {
    document.getElementById('method_direct').classList.toggle('hidden', method !== 'direct');
    document.getElementById('method_nowpayments').classList.toggle('hidden', method !== 'nowpayments');

    const activeClass = ['border-gray-900', 'dark:border-white', 'bg-gray-900', 'dark:bg-white', 'text-white', 'dark:text-gray-900'];
    const inactiveClass = ['border-gray-200', 'dark:border-gray-700', 'text-gray-600', 'dark:text-gray-400'];

    ['direct', 'nowpayments'].forEach(m => {
        const btn = document.getElementById('tab_' + m);
        if (m === method) {
            inactiveClass.forEach(c => btn.classList.remove(c));
            activeClass.forEach(c => btn.classList.add(c));
        } else {
            activeClass.forEach(c => btn.classList.remove(c));
            inactiveClass.forEach(c => btn.classList.add(c));
        }
    });
}
</script>
@endsection
