@extends(auth('unified')->check() ? 'client.layouts.app' : 'client.layouts.app')
@section('title', __('client.transfers_title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">

    {{-- Баланси --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
            <p class="text-xs text-gray-400 mb-1">{{ __('client.client_balance') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($clientWallet->balance ?? 0, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
            <p class="text-xs text-gray-400 mb-1">{{ __('client.webmaster_balance') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($webmasterWallet->balance ?? 0, 2) }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Форма переказу --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.transfer_title') }}</h2>

        <form method="POST" action="{{ route(request()->is('wm/*') ? 'webmaster.wallet.transfer' : 'client.wallet.transfer') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.transfer_direction') }}</label>
                <div class="flex gap-2">
                    <button type="button" onclick="setDir('client_to_webmaster')" id="dir_c2w"
                        class="flex-1 py-2 px-3 rounded-lg text-sm border-2 border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900">
                        {{ __('client.transfer_c2w') }}
                    </button>
                    <button type="button" onclick="setDir('webmaster_to_client')" id="dir_w2c"
                        class="flex-1 py-2 px-3 rounded-lg text-sm border-2 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                        {{ __('client.transfer_w2c') }}
                    </button>
                </div>
                <input type="hidden" name="direction" id="direction_input" value="client_to_webmaster">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.amount_usd') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                    <input type="number" name="amount" min="0.01" step="0.01"
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                           placeholder="10.00">
                </div>
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.transfer_note') }}</label>
                <input type="text" name="note" maxlength="200"
                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                       placeholder="{{ __('client.transfer_note_placeholder') }}">
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-3 mb-4 text-xs text-blue-700 dark:text-blue-400">
                {{ __('client.transfer_no_fee_note') }}
            </div>

            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                {{ __('client.transfer_submit') }}
            </button>
        </form>
    </div>

    {{-- Історія --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.transfer_history') }}</h2>

        @forelse($transfers as $t)
        <div class="flex items-start justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
            <div>
                <p class="text-sm text-gray-800 dark:text-gray-200">
                    @if($t->direction === 'client_to_webmaster')
                        {{ __('client.transfer_c2w') }}
                    @else
                        {{ __('client.transfer_w2c') }}
                    @endif
                </p>
                @if($t->note)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $t->note }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-0.5">{{ $t->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($t->amount, 2) }}</p>
                <p class="mt-0.5">{{ __('client.client_balance') }}: ${{ number_format($t->client_balance_after, 2) }}</p>
                <p>{{ __('client.webmaster_balance') }}: ${{ number_format($t->webmaster_balance_after, 2) }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">{{ __('client.transfer_no_history') }}</p>
        @endforelse

        @if($transfers->hasPages())
        <div class="mt-4">{{ $transfers->links() }}</div>
        @endif
    </div>
</div>

<script>
function setDir(dir) {
    document.getElementById('direction_input').value = dir;
    const active = ['border-gray-900','dark:border-white','bg-gray-900','dark:bg-white','text-white','dark:text-gray-900'];
    const inactive = ['border-gray-200','dark:border-gray-700','text-gray-600','dark:text-gray-400'];
    ['c2w','w2c'].forEach(k => {
        const btn = document.getElementById('dir_' + k);
        const isActive = (k === 'c2w' && dir === 'client_to_webmaster') || (k === 'w2c' && dir === 'webmaster_to_client');
        if (isActive) { inactive.forEach(c => btn.classList.remove(c)); active.forEach(c => btn.classList.add(c)); }
        else { active.forEach(c => btn.classList.remove(c)); inactive.forEach(c => btn.classList.add(c)); }
    });
}
</script>
@endsection
