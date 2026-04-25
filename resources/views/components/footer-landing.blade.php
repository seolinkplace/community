@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
@endphp
<footer>
    <a href="{{ $localeService->urlForLocale($currentLocale, '/') }}" class="logo">{{ config('app.name') }}</a>
    <p>&copy; 2011&ndash;{{ date('Y') }} {{ config('app.name') }}. {{ __('common.all_rights_reserved') }}</p>
    <div class="footer-links">
        <a href="{{ $localeService->urlForLocale($currentLocale, '/blog') }}">{{ __('nav.blog') }}</a> ·
        <a href="{{ $localeService->urlForLocale($currentLocale, '/contact') }}">{{ __('nav.contact') }}</a> ·
        <a href="/privacy">{{ __('common.privacy_policy') }}</a>
    </div>
</footer>
