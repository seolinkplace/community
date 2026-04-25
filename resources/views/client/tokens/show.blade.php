@extends('client.layouts.app')
@section('title', __('client.tokens_title'))
@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.tokens.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $token->site->domain ?? '—' }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('client.token') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ __('client.token_use_integrations') }}
            {{-- посилання на інтеграції відсутнє в клієнтській частині, просто інфо --}}
        </p>

        <div class="flex items-center gap-2 mb-4">
            <code class="flex-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 rounded-lg text-sm font-mono break-all select-all">{{ $token->token }}</code>
            <button onclick="navigator.clipboard.writeText('{{ $token->token }}'); this.classList.add('bg-green-600'); this.innerHTML='<svg class='w-4 h-4 text-white' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>'"
                    class="flex-shrink-0 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-3 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="font-medium {{ $token->status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                ● {{ $token->status === 'active' ? __('client.status_active') : __('client.status_revoked') }}
            </span>
            <span>{{ __('client.limit') }}: <strong class="text-gray-700 dark:text-gray-300">{{ $token->link_limit }}</strong></span>
            <span>{{ __('client.last_used') }}: <strong class="text-gray-700 dark:text-gray-300">{{ $token->last_used_at?->diffForHumans() ?? __('client.never') }}</strong></span>
        </div>

        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 inline-block mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('client.token_snippet_hint') }}
                Зверніться до вашого вебмайстра або відвідайте розділ <strong>Інтеграції</strong> для отримання інструкцій з підключення.
            </p>
        </div>
    </div>
</div>
@endsection
