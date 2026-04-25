@extends('client.layouts.app')
@section('title', __('direct_payments.new'))
@section('content')
<div class="max-w-2xl mx-auto py-6 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.direct-payments.index') }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('direct_payments.new') }}</h1>
    </div>

    @if($webmaster && $webmaster->webmasterProfile?->usdt_address)
        {{-- USDT address info block --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 mb-6">
            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                {{ __('direct_payments.send_to_address') }}
            </p>
            <div class="flex items-center gap-2">
                <code class="flex-1 text-xs bg-white dark:bg-gray-900 border border-yellow-200 dark:border-yellow-700 rounded px-3 py-2 text-gray-800 dark:text-gray-200 break-all select-all">
                    {{ $webmaster->webmasterProfile->usdt_address }}
                </code>
                <button type="button"
                        onclick="navigator.clipboard.writeText('{{ $webmaster->webmasterProfile->usdt_address }}')"
                        class="flex-shrink-0 px-3 py-2 text-xs bg-yellow-400 hover:bg-yellow-500 text-black rounded-lg transition font-medium">
                    {{ __('common.copy') }}
                </button>
            </div>
            <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-2">
                {{ __('direct_payments.then_confirm') }}
            </p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.direct-payments.store') }}">
            @csrf

            @if($webmaster)
                <input type="hidden" name="webmaster_id" value="{{ $webmaster->id }}">
                <div class="mb-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('direct_payments.webmaster') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $webmaster->name }}</p>
                </div>
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('direct_payments.webmaster') }}
                    </label>
                    <input type="number" name="webmaster_id" value="{{ old('webmaster_id') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('direct_payments.amount') }}
                </label>
                <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('direct_payments.note') }}
                </label>
                <textarea name="note" rows="3"
                          placeholder="{{ __('direct_payments.note_placeholder') }}"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('note') }}</textarea>
            </div>

            {{-- Agreement --}}
            <div class="mb-6 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="agreement" value="1"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-yellow-400 focus:ring-yellow-400"
                           {{ old('agreement') ? 'checked' : '' }}>
                    <span class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('direct_payments.agreement_text') }}
                    </span>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-yellow-400 hover:bg-yellow-500 text-black text-sm font-semibold rounded-lg transition">
                {{ __('direct_payments.new') }}
            </button>
        </form>
    </div>
</div>
@endsection
