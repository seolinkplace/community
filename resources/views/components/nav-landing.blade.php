@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
    $allLocales    = $localeService->all();
    $primaryLocales    = $localeService->primary();
    $secondaryLocales  = $localeService->secondary();

    $homeUrl    = $localeService->urlForLocale($currentLocale, '/');
    $blogUrl    = $localeService->urlForLocale($currentLocale, '/blog');
    $contactUrl = $localeService->urlForLocale($currentLocale, '/contact');
    $rulesUrl   = $localeService->urlForLocale($currentLocale, '/rules');
@endphp
<link rel="stylesheet" href="/vendor/flag-icons/css/flag-icons.min.css">
<nav>
    <div class="nav-left">
        <a href="{{ $homeUrl }}" class="logo">{{ config('app.name') }}</a>
        <div class="nav-links">
            @if($landing ?? false)
                <a href="#how" onclick="closeMobile()">{{ __('nav.how_it_works') }}</a>
                <a href="#for-who" onclick="closeMobile()">{{ __('nav.for_who') }}</a>
                <a href="#tasks" onclick="closeMobile()">{{ __('nav.tasks') }}</a>
                <a href="{{ route('unified.register') }}" onclick="closeMobile()">{{ __('nav.sign_up') }}</a>
            @endif
            <a href="{{ $blogUrl }}" {{ request()->is('blog*') || request()->is('*/blog*') ? 'style=color:#eab308;font-weight:700;' : '' }}>{{ __('nav.blog') }}</a>
            <a href="{{ $contactUrl }}">{{ __('nav.contact') }}</a>
            <a href="{{ $rulesUrl }}" {{ request()->is('rules*') ? 'style=color:#eab308;font-weight:700;' : '' }}>{{ __('nav.rules') }}</a>
        </div>
    </div>
    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle"
                title="{{ __('nav.toggle_theme') }}"
                aria-label="{{ __('nav.toggle_theme') }}">
            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <a href="{{ route('unified.login') }}" class="nav-login">{{ __('nav.sign_in') }}</a>
        <a href="{{ route('unified.register') }}" class="nav-register">{{ __('nav.sign_up') }}</a>

        {{-- Primary locales: direct buttons --}}
        @foreach($primaryLocales as $code => $config)
            @if($code !== $currentLocale)
                <a href="{{ route('lang.switch', $code) }}" class="nav-lang" title="{{ $config['name'] }}">
                    <span class="fi fi-{{ $config['flag'] }}"></span>
                </a>
            @endif
        @endforeach

        {{-- Secondary locales: dropdown --}}
        @if(count($secondaryLocales) > 0)
        <div class="nav-lang-dropdown" id="langDropdown">
            <button class="nav-lang-btn" id="langDropdownBtn" aria-label="{{ __('nav.language') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </button>
            <div class="nav-lang-menu" id="langDropdownMenu">
                @foreach($secondaryLocales as $code => $config)
                    <a href="{{ route('lang.switch', $code) }}"
                       class="nav-lang-item {{ $code === $currentLocale ? 'active' : '' }}">
                        <span class="fi fi-{{ $config['flag'] }}"></span>
                        <span>{{ $config['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <button class="nav-burger" id="navBurger" aria-label="{{ __('nav.menu') }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
</nav>
<div class="nav-mobile" id="navMobile">
    @if($landing ?? false)
        <a href="#how" onclick="closeMobile()">{{ __('nav.how_it_works') }}</a>
        <a href="#for-who" onclick="closeMobile()">{{ __('nav.for_who') }}</a>
        <a href="#tasks" onclick="closeMobile()">{{ __('nav.tasks') }}</a>
        <a href="{{ route('unified.register') }}" onclick="closeMobile()">{{ __('nav.sign_up') }}</a>
    @endif
    <a href="{{ $blogUrl }}" onclick="closeMobile()">{{ __('nav.blog') }}</a>
    <a href="{{ $contactUrl }}" onclick="closeMobile()">{{ __('nav.contact') }}</a>
    <a href="{{ $rulesUrl }}" onclick="closeMobile()">{{ __('nav.rules') }}</a>
    <a href="{{ route('unified.login') }}">{{ __('nav.sign_in') }}</a>
    {{-- All locales in mobile menu --}}
    <div class="nav-mobile-langs">
        @foreach($allLocales as $code => $config)
            @if($code !== $currentLocale)
                <a href="{{ route('lang.switch', $code) }}">
                    <span class="fi fi-{{ $config['flag'] }}"></span>
                    {{ $config['name'] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
<script>
(function(){
    var btn = document.getElementById('langDropdownBtn');
    var menu = document.getElementById('langDropdownMenu');
    if (btn && menu) {
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            menu.classList.toggle('open');
        });
        document.addEventListener('click', function(){
            menu.classList.remove('open');
        });
    }
})();
</script>
