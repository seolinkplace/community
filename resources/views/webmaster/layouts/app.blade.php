<!DOCTYPE html>
<html lang="uk" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '{{ config('app.name') }}') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YJ6ZE1CCFQ"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-YJ6ZE1CCFQ');
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200">

@if(session("impersonating_as"))
<div class="bg-violet-600 text-white text-xs px-4 py-2 flex justify-between items-center">
    <span>⚡ {{ __('nav.role_admin') }}</span>
    <a href="{{ route('admin.impersonate.leave') }}" class="underline">← Повернутись</a>
</div>
@endif

<div class="flex h-full min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen=false"
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"
         x-cloak></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside x-cloak
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 left-0 z-30 w-56 flex-shrink-0 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 min-h-screen transition-transform duration-200 ease-in-out">

        {{-- Logo --}}
        <div class="h-14 flex items-center px-4 border-b border-gray-200 dark:border-gray-800">
            <a href="{{ route('webmaster.dashboard') }}" class="font-bold text-base tracking-tight" style="font-family:'Syne',sans-serif;font-weight:800;font-size:18px;color:inherit;text-decoration:none">{{ config('app.name') }}</a>

        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
                $_wmChatUnread = 0;
                $_wmOrdersPending = 0;
                try {
                    $_wmId = \App\Helpers\AuthHelper::webmasterId();
                    if ($_wmId) {
                        $_siteIds = \App\Models\Site::where('webmaster_id', $_wmId)->pluck('id');
                        $_wmChatUnread = \App\Models\ChatMessage::where('sender_type', 'client')
                            ->whereNull('read_at')
                            ->whereHas('campaignLink', fn($q) => $q->whereIn('site_id', $_siteIds))
                            ->count();
                        $_wmOrdersPending = \App\Models\CampaignLink::whereIn('site_id', $_siteIds)
                            ->where('status', 'pending')
                            ->count();
                    }
                } catch(\Exception $e) {}
                $_taskPending = 0;
                try {
                    $_taskUser = \App\Helpers\AuthHelper::webmaster();
                    if ($_taskUser) {
                        $_taskPending = \App\Models\TaskCompletion::where('status', 'pending')
                            ->whereHas('task', fn($q) => $q->where('creator_type', \App\Models\Webmaster::class)->where('creator_id', $_taskUser->id))
                            ->count();
                    }
                } catch(\Exception $e) {}
            @endphp

            @php
            $navLink = function(string $route, string $label, string $icon, string $active = '', $badge = null, $badgeColor = 'blue') use (&$navLink) {
                $isActive = $active ? request()->routeIs($active) : request()->routeIs($route);
                $cls = $isActive
                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white';
                return [$cls, $isActive];
            };
            @endphp

            {{-- Dashboard --}}
            @php [$cls] = $navLink('webmaster.dashboard', '', ''); @endphp
            <a href="{{ route('webmaster.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('nav.dashboard') }}
            </a>

            <div class="pt-3 pb-1 px-3"><p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('nav.section_sites') }}</p></div>

            <a href="{{ route('webmaster.sites.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.sites.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                {{ __('nav.sites') }}
            </a>

            <a href="{{ route('webmaster.pages.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.pages.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('nav.pages') }}
            </a>

            <a href="{{ route('webmaster.prices.links') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.prices.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ __('nav.prices') }}
            </a>

            <div class="pt-3 pb-1 px-3"><p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('nav.section_content') }}</p></div>

            <a href="{{ route('webmaster.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.orders.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="flex-1">{{ __('nav.orders') }}</span>
                @if($_wmOrdersPending > 0)
                <span class="bg-blue-600 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $_wmOrdersPending }}</span>
                @endif
            </a>

            <a href="{{ route('webmaster.chat.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.chat.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="flex-1">{{ __('nav.chat') }}</span>
                @if($_wmChatUnread > 0)
                <span class="bg-blue-600 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $_wmChatUnread }}</span>
                @endif
            </a>

            <a href="{{ route('webmaster.tasks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.tasks.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span class="flex-1">{{ __('nav.tasks') }}</span>
                @if($_taskPending > 0)
                <span class="bg-orange-500 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $_taskPending }}</span>
                @endif
            </a>

            <a href="{{ route('webmaster.articles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.articles.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                {{ __('nav.articles') }}
                @php
                    $wm = \App\Helpers\AuthHelper::webmaster();
                    $pendingRevisions = $wm ? \App\Models\Article::whereIn('site_id', $wm->sites()->pluck('id'))
                        ->where('status', 'revision_requested')->count() : 0;
                @endphp
                @if($pendingRevisions > 0)
                <span class="ml-auto text-xs bg-orange-500 text-white rounded-full px-1.5 py-0.5 min-w-[1.25rem] text-center">{{ $pendingRevisions }}</span>
                @endif
            </a>

            <div class="pt-3 pb-1 px-3"><p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('nav.section_finance') }}</p></div>

            <a href="{{ route('webmaster.wallet') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.wallet') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="flex-1">{{ __('nav.wallet') }}</span>
                @php $wmBalance = \App\Helpers\AuthHelper::webmaster()->webmasterWallet?->balance ?? 0; @endphp
                <span class="text-xs text-gray-400">${{ $wmBalance >= 1000 ? number_format($wmBalance, 0) : number_format($wmBalance, 2) }}</span>
            </a>

            <a href="{{ route('webmaster.stats') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.stats') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('nav.stats') }}
            </a>

            <a href="{{ route('webmaster.support.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.support.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="flex-1">{{ __('nav.support') }}</span>
            </a>
            <a href="{{ route('webmaster.affiliate.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('nav.affiliate') }}
            </a>

            <a href="{{ route('webmaster.subscription.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.subscription.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                {{ __('nav.subscription') }}
            </a>
            @if(\App\Helpers\AuthHelper::webmaster()->webmasterProfile?->direct_payments_enabled)
            <a href="{{ route('webmaster.direct-payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.direct-payments.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                {{ __('nav.direct_payments') }}
            </a>
            @endif
            <a href="{{ route('webmaster.integrations') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('webmaster.integrations') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7m-5-9l5 5m0 0v4m0-4h-4"/></svg>
                {{ __('client.integrations') }}
            </a>
        </nav>

        {{-- User block --}}
        <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-800" x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <span class="w-7 h-7 bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <div class="flex-1 text-left min-w-0">
                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ \App\Helpers\AuthHelper::webmaster()->name }}</p>
                    <p class="text-xs font-medium text-green-600">{{ __('nav.role_webmaster') }}</p>
                </div>
                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" @click.away="open=false" x-cloak
                 class="mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1">
                @if(\App\Helpers\AuthHelper::webmaster() instanceof \App\Models\UnifiedUser && \App\Helpers\AuthHelper::webmaster()->hasRole('client'))
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg> {{ __('nav.switch_to_client') }}
                </a>
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                @endif
                <a href="{{ route('webmaster.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('nav.profile') }}
                </a>

                </div>
                <form method="POST" action="{{ route('webmaster.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        {{ __('nav.logout') }}
                    </button>
                </form>
        </div>
    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-14 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center px-4 gap-4 flex-shrink-0">
            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen=true" class="lg:hidden text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="text-sm font-semibold text-gray-900 dark:text-white flex-1 truncate">@yield('title', __('nav.dashboard'))</h1>
                        <div class="flex items-center gap-1 ml-auto">
                <button onclick="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                    class="p-1.5 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <div class="w-px h-4 bg-gray-200 dark:bg-gray-700"></div>
                <x-locale-switcher />
            </div>
            @yield('header_actions')
            {{-- Header actions --}}
            <div class="flex items-center gap-1 ml-1">
                @php
                    $_switchRoles = array_diff(auth('unified')->user()?->activeRoles() ?? [], ['webmaster']);
                    $_switchCount = count($_switchRoles);
                @endphp
                @if($_switchCount === 1)
                    @php $_switchRole = reset($_switchRoles); @endphp
                    @php $_switchRoute = match($_switchRole) { 'client' => route('client.dashboard'), 'performer' => route('performer.dashboard'), default => route('unified.dashboard') }; @endphp
                    <a href="{{ $_switchRoute }}" title="{{ __('nav.choose_cabinet') }}"
                       class="p-1.5 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </a>
                @elseif($_switchCount > 1)
                    <a href="{{ route('unified.dashboard') }}" title="{{ __('nav.choose_cabinet') }}"
                       class="p-1.5 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </a>
                @endif
                <a href="{{ route('webmaster.profile.edit') }}" title="{{ __('nav.profile') }}"
                   class="p-1.5 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                <form method="POST" action="{{ route('webmaster.logout') }}">
                    @csrf
                    <button type="submit" title="{{ __('nav.logout') }}"
                        class="p-1.5 rounded text-gray-400 hover:text-red-600 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </header>

        <div class="px-4 lg:px-6 pt-4">
            @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif
        </div>

        <main class="flex-1 px-4 lg:px-6 py-4 overflow-auto pb-24 lg:pb-4" style="padding-bottom:80px">
            @yield('content')
        </main>
    </div>
</div>

<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('modals')
    @stack('scripts')
@include('components.cookie-banner')
</body>
</html>
