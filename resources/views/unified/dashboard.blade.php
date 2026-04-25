@extends('unified.layouts.app')
@section('title', __('nav.dashboard'))
@section('content')

@php
    $hasClient     = in_array('client', $roles);
    $hasWebmaster  = in_array('webmaster', $roles);
    $hasPerformer  = in_array('performer', $roles);
    $roleCount     = count($roles);

    // Якщо тільки одна роль — редіректимо одразу
    if ($roleCount === 1) {
        if ($hasClient)    { header('Location: ' . route('client.dashboard'));    exit; }
        if ($hasWebmaster) { header('Location: ' . route('webmaster.dashboard')); exit; }
        if ($hasPerformer) { header('Location: ' . route('client.tasks.index'));  exit; }
    }
@endphp

<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ __('nav.greeting', ['name' => $user->name]) }}</h1>
    <p class="text-sm text-gray-400 dark:text-gray-500 mb-8">{{ __('nav.choose_cabinet') }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        @if($hasClient)
        <a href="{{ route('client.dashboard') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('nav.role_client') }}</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('nav.role_client_desc') }}</p>
        </a>
        @endif

        @if($hasWebmaster)
        <a href="{{ route('webmaster.dashboard') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('nav.role_webmaster') }}</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('nav.role_webmaster_desc') }}</p>
        </a>
        @endif

        @if($hasPerformer)
        <a href="{{ route('client.tasks.index') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('nav.role_performer') }}</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('nav.role_performer_desc') }}</p>
        </a>
        @endif

        {{-- Додати роль тільки якщо немає всіх ролей --}}
        @if(!$hasClient || !$hasWebmaster)
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-6">
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-400 dark:text-gray-500">{{ __('nav.add_role') }}</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                @if(!$hasClient && !$hasWebmaster)
                    {{ __('nav.add_role_desc_both') }}
                @elseif(!$hasClient)
                    {{ __('nav.add_role_desc_client') }}
                @else
                    {{ __('nav.add_role_desc_webmaster') }}
                @endif
            </p>
        </div>
        @endif

    </div>
</div>
@endsection
