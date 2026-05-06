@extends('unified.layouts.app')
@section('title', __('client.onboarding_title'))
@section('content')

@php $roles = $user->activeRoles(); @endphp

<div class="max-w-2xl mx-auto py-10 px-4">

    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 mb-4">
            <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('client.onboarding_welcome') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mx-auto">{{ __('client.onboarding_subtitle') }}</p>
    </div>

    <div class="space-y-4">

        @if(in_array('client', $roles))
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('client.onboarding_role_client') }}</span>
            </div>
            <div class="p-5">
                <ol class="space-y-3 mb-5">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-xs font-bold text-yellow-700 dark:text-yellow-400 mt-0.5">1</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_step1_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_step1_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-xs font-bold text-yellow-700 dark:text-yellow-400 mt-0.5">2</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_step2_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_step2_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-xs font-bold text-yellow-700 dark:text-yellow-400 mt-0.5">3</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_step3_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_step3_desc') }}</p>
                        </div>
                    </li>
                </ol>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('client.wallet') }}" class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        {{ __('client.top_up_balance') }}
                    </a>
                    <a href="{{ route('client.catalog.index') }}" class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        {{ __('client.go_to_catalog') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('webmaster', $roles))
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('client.onboarding_role_webmaster') }}</span>
            </div>
            <div class="p-5">
                <ol class="space-y-3 mb-5">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-400 mt-0.5">1</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_wm_step1_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_wm_step1_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-400 mt-0.5">2</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_wm_step2_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_wm_step2_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-400 mt-0.5">3</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_wm_step3_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_wm_step3_desc') }}</p>
                        </div>
                    </li>
                </ol>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('webmaster.sites.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('client.add_placement') }}
                    </a>
                    <a href="{{ route('webmaster.integrations') }}" class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        {{ __('client.onboarding_integrations') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('performer', $roles))
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('client.onboarding_role_performer') }}</span>
            </div>
            <div class="p-5">
                <ol class="space-y-3 mb-5">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-xs font-bold text-green-700 dark:text-green-400 mt-0.5">1</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_pf_step1_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_pf_step1_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-xs font-bold text-green-700 dark:text-green-400 mt-0.5">2</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_pf_step2_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_pf_step2_desc') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-xs font-bold text-green-700 dark:text-green-400 mt-0.5">3</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('client.onboarding_pf_step3_title') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('client.onboarding_pf_step3_desc') }}</p>
                        </div>
                    </li>
                </ol>
                <a href="{{ route('performer.tasks.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    {{ __('client.onboarding_view_tasks') }}
                </a>
            </div>
        </div>
        @endif

    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">{{ __('client.onboarding_revisit_hint') }}</p>
        <form method="POST" action="{{ route('unified.onboarding.dismiss') }}" class="flex-shrink-0">
            @csrf
            <input type="hidden" name="redirect" value="{{ route('unified.dashboard') }}">
            <button type="submit" class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                {{ __('client.onboarding_done') }}
            </button>
        </form>
    </div>

</div>
@endsection
