@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = app()->getLocale();
    $primaryLocales = $localeService->primary();
    $secondaryLocales = $localeService->secondary();
    $allLocales = $localeService->all();
@endphp
<div class="flex items-center gap-1" style="position:relative;">
    {{-- Primary locales --}}
    @foreach($primaryLocales as $code => $config)
        <a href="{{ route('lang.switch', $code) }}"
           class="px-1.5 py-0.5 rounded text-xs font-semibold transition-colors {{ $code === $currentLocale ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach

    {{-- Current locale if it's secondary --}}
    @if(array_key_exists($currentLocale, $secondaryLocales))
        <a href="{{ route('lang.switch', $currentLocale) }}"
           class="px-1.5 py-0.5 rounded text-xs font-semibold transition-colors bg-gray-900 dark:bg-white text-white dark:text-gray-900">
            {{ strtoupper($currentLocale) }}
        </a>
    @endif

    {{-- Secondary dropdown --}}
    @if(count($secondaryLocales) > 0)
        <div style="position:relative;" id="ls-wrap-{{ md5($currentLocale) }}">
            <button onclick="document.getElementById('ls-menu-{{ md5($currentLocale) }}').classList.toggle('ls-open')"
                    class="p-1 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                    title="{{ __('nav.language') }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </button>
            <div id="ls-menu-{{ md5($currentLocale) }}"
                 style="display:none;position:absolute;right:0;top:100%;margin-top:4px;background:white;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);min-width:120px;z-index:999;padding:4px 0;">
                @foreach($secondaryLocales as $code => $config)
                    <a href="{{ route('lang.switch', $code) }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;font-size:12px;color:{{ $code === $currentLocale ? '#0f172a' : '#64748b' }};font-weight:{{ $code === $currentLocale ? '600' : '400' }};text-decoration:none;"
                       onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <span class="fi fi-{{ $config['flag'] }}"></span>
                        {{ $config['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<link rel="stylesheet" href="/vendor/flag-icons/css/flag-icons.min.css">
<style>
.ls-open { display:block !important; }
</style>
<script>
document.addEventListener('click', function(e) {
    var menus = document.querySelectorAll('[id^="ls-menu-"]');
    menus.forEach(function(m) {
        if (!m.parentElement.contains(e.target)) m.classList.remove('ls-open');
    });
});
</script>
