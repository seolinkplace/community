<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="transition-colors duration-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'seolinkplace') — seolinkplace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-200">

<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-14">
            {{-- Logo --}}
            <a href="{{ route('unified.dashboard') }}" class="font-bold text-base tracking-tight flex-shrink-0" style="font-family:'Syne',sans-serif;font-weight:800;font-size:18px;color:inherit;text-decoration:none">{{ config('app.name') }}</a>

            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-4">
                <a href="{{ route('unified.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('unified.dashboard') ? 'font-medium text-gray-900 dark:text-white' : '' }}">{{ __('nav.dashboard') }}</a>
                <a href="{{ route('unified.affiliate.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('unified.affiliate.*') ? 'font-medium text-gray-900 dark:text-white' : '' }}">{{ __('nav.affiliate') }}</a>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">
                @php $user = auth('unified')->user(); @endphp

                {{-- Role switchers --}}
                @if($user?->hasRole('client'))
                <a href="{{ route('client.dashboard') }}" class="hidden sm:inline-flex text-xs px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">{{ __('nav.role_client') }}</a>
                @endif
                @if($user?->hasRole('webmaster'))
                <a href="{{ route('webmaster.dashboard') }}" class="hidden sm:inline-flex text-xs px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">{{ __('nav.role_webmaster') }}</a>
                @endif

                {{-- Lang switcher --}}
                <x-locale-switcher />

                {{-- Theme toggle --}}
                <button onclick="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                {{-- Mobile menu button --}}
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="sm:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                {{-- Logout desktop --}}
                <form method="POST" action="{{ route('unified.logout') }}" class="hidden sm:block">
                    @csrf
                    <button class="text-sm text-red-500 hover:text-red-700">{{ __('nav.logout') }}</button>
                </form>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-200 dark:border-gray-700 py-3 space-y-1">
            <a href="{{ route('unified.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('nav.dashboard') }}</a>
            <a href="{{ route('unified.affiliate.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('nav.affiliate') }}</a>
            @if($user?->hasRole('client'))
            <a href="{{ route('client.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">→ {{ __('nav.role_client') }}</a>
            @endif
            @if($user?->hasRole('webmaster'))
            <a href="{{ route('webmaster.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">→ {{ __('nav.role_webmaster') }}</a>
            @endif
            <form method="POST" action="{{ route('unified.logout') }}" class="px-3">
                @csrf
                <button class="text-sm text-red-500 hover:text-red-700 py-2">{{ __('nav.logout') }}</button>
            </form>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
</main>
@stack('modals')
@stack('scripts')
</body>
</html>
