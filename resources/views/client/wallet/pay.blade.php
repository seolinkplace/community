@extends('client.layouts.app')
@section('title', __('client.pay_usdt_title'))

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">

        {{-- Заголовок --}}
        <div class="text-center mb-6">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('client.pay_amount_label') }}</p>
            <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $meta['unique_amount'] }}</p>
            <p class="text-lg text-gray-400 dark:text-gray-500">USDT TRC20</p>
        </div>

        {{-- Таймер --}}
        <div class="text-center mb-6">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.pay_time_label') }}</p>
            <p class="text-3xl font-mono font-bold text-gray-900 dark:text-white" id="timer">60:00</p>
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1 mt-2">
                <div id="timer-bar" class="bg-gray-900 dark:bg-white h-1 rounded-full transition-all duration-1000" style="width:100%"></div>
            </div>
        </div>

        {{-- QR варіанти --}}
        <div class="mb-4">
            <div class="flex gap-2 mb-3">
                <button onclick="switchQR('standard')" id="btn_standard"
                    class="flex-1 py-1.5 text-xs font-medium rounded-lg border-2 border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900">
                    {{ __('client.pay_qr_standard') }}
                </button>
                <button onclick="switchQR('binance')" id="btn_binance"
                    class="flex-1 py-1.5 text-xs font-medium rounded-lg border-2 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                    {{ __('client.pay_qr_binance') }}
                </button>
            </div>

            <div class="flex justify-center">
                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white">
                    <img id="qr-img"
                         src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($wallet) }}"
                         alt="QR" width="200" height="200" class="rounded">
                </div>
            </div>
            <p id="qr-hint" class="text-xs text-center text-gray-400 mt-2">{{ __('client.pay_qr_hint_standard') }}</p>
        </div>

        {{-- Адреса --}}
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mb-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.pay_wallet_label') }}</p>
            <div class="flex items-center gap-2">
                <code class="text-xs text-gray-800 dark:text-gray-200 break-all flex-1">{{ $wallet }}</code>
                <button onclick="copyText('{{ $wallet }}', this)"
                    class="shrink-0 text-xs text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Копіювати
                </button>
            </div>
        </div>

        {{-- Сума --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-3">
            <p class="text-xs text-amber-700 dark:text-amber-400 mb-1 font-medium">{{ __('client.pay_exact_amount') }}</p>
            <div class="flex items-center gap-2">
                <code class="text-lg font-bold text-amber-900 dark:text-amber-300 flex-1">{{ $meta['unique_amount'] }} USDT</code>
                <button onclick="copyText('{{ $meta['unique_amount'] }}', this)"
                    class="shrink-0 text-xs text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded px-2 py-1 hover:bg-amber-100 dark:hover:bg-amber-900/40">
                    Копіювати
                </button>
            </div>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">{{ __('client.pay_unique_hint') }}</p>
        </div>

        {{-- Гаманець відправника --}}
        @if(!empty($meta['from_wallet']))
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mb-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.pay_from_wallet') }}</p>
            <code class="text-xs text-gray-700 dark:text-gray-300 break-all">{{ $meta['from_wallet'] }}</code>
        </div>
        @endif

        {{-- Інструкція для Binance --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4 text-xs text-blue-700 dark:text-blue-400">
            <p class="font-medium mb-1">{{ __('client.pay_binance_title') }}</p>
            <p>{{ __('client.pay_binance_hint') }}</p>
            <p class="mt-1 text-blue-500 dark:text-blue-500">{{ __('client.pay_binance_warning') }}</p>
        </div>

        {{-- Статус --}}
        <div id="status-block" class="rounded-lg p-3 mb-4 text-center bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-700 dark:text-blue-400" id="status-text">{{ __('client.pay_waiting') }}</p>
        </div>

        <a href="{{ route('client.wallet') }}"
           class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            ← {{ __('client.back') }}
        </a>
    </div>
</div>

<script>
const WALLET = '{{ $wallet }}';
const AMOUNT = '{{ $meta['unique_amount'] }}';
const expiresAt = new Date('{{ $meta['expires_at'] }}');
const totalSeconds = 60 * 60;

function updateTimer() {
    const diff = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
    const m = String(Math.floor(diff / 60)).padStart(2, '0');
    const s = String(diff % 60).padStart(2, '0');
    document.getElementById('timer').textContent = m + ':' + s;
    document.getElementById('timer-bar').style.width = (diff / totalSeconds * 100) + '%';

    if (diff === 0) {
        document.getElementById('status-text').textContent = '{{ __("client.pay_expired") }}';
        document.getElementById('status-block').className = 'rounded-lg p-3 mb-4 text-center bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
        document.getElementById('status-text').className = 'text-sm text-red-600 dark:text-red-400';
    }
}
setInterval(updateTimer, 1000);
updateTimer();

// QR switcher
function switchQR(type) {
    const qrImg = document.getElementById('qr-img');
    const hint  = document.getElementById('qr-hint');
    const btnStd = document.getElementById('btn_standard');
    const btnBin = document.getElementById('btn_binance');

    const active   = 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900';
    const inactive = 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400';

    if (type === 'standard') {
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(WALLET)}`;
        hint.textContent = '{{ __('client.pay_qr_hint_standard') }}';
        btnStd.className = btnStd.className.replace(inactive, '') + ' ' + active;
        btnBin.className = btnBin.className.replace(active, '') + ' ' + inactive;
    } else {
        // Формат для Binance та інших гаманців що розуміють tron: URI
        const data = `tron:${WALLET}?amount=${AMOUNT}&token=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t`;
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(data)}`;
        hint.textContent = '{{ __("client.pay_qr_hint_binance") }}';
        btnBin.className = btnBin.className.replace(inactive, '') + ' ' + active;
        btnStd.className = btnStd.className.replace(active, '') + ' ' + inactive;
    }
}

// Polling
function checkStatus() {
    fetch('{{ route('client.wallet.pay.status', ['tx' => $tx->external_id]) }}')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'completed') {
                document.getElementById('status-text').textContent = '{{ __("client.pay_success") }}';
                document.getElementById('status-block').className = 'rounded-lg p-3 mb-4 text-center bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
                document.getElementById('status-text').className = 'text-sm text-green-700 dark:text-green-400';
                setTimeout(() => window.location = '{{ route('client.wallet') }}?payment=success', 2000);
            }
        })
        .catch(() => {});
}
setInterval(checkStatus, 15000);

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '{{ __("client.copied") }}';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}
</script>
@endsection
