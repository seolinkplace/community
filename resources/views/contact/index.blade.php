@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
    $allLocales    = $localeService->all();
    $baseUrl       = rtrim(config('app.url'), '/');
    $currentPrefix = $localeService->prefix($currentLocale);
    $canonicalUrl  = $baseUrl . $localeService->urlForLocale($currentLocale, '/contact');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.form_title') }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('contact.form_subtitle') }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($allLocales as $code => $config)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $baseUrl . $localeService->urlForLocale($code, '/contact') }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/contact">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ __('contact.form_title') }} — {{ config('app.name') }}">
    <meta property="og:description" content="{{ __('contact.form_subtitle') }}">
    <meta property="og:image" content="{{ $baseUrl }}/images/og-home.png">

    <link rel="stylesheet" href="/css/landing.css">
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YJ6ZE1CCFQ"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-YJ6ZE1CCFQ');</script>
</head>
<body>
@include('components.nav-landing', ['landing' => false])
<section style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:60px 20px;">
    <div style="width:100%;max-width:520px;">
        <h1 style="font-size:28px;font-weight:800;margin-bottom:8px;">{{ __('contact.form_title') }}</h1>
        <p style="color:var(--text-muted);margin-bottom:16px;font-size:15px;">{{ __('contact.form_subtitle') }}</p>
        <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:28px;font-size:13px;color:var(--text-muted);line-height:1.6;">
            {{ __('contact.form_reply_hint') }}
        </div>

        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px 16px;margin-bottom:20px;color:#991b1b;font-size:14px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:var(--text);">{{ __('contact.form_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:var(--card-bg);color:var(--text);font-size:14px;outline:none;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:var(--text);">{{ __('contact.form_email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:var(--card-bg);color:var(--text);font-size:14px;outline:none;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:var(--text);">{{ __('contact.form_message') }}</label>
                <textarea name="message" rows="5" required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:var(--card-bg);color:var(--text);font-size:14px;outline:none;resize:vertical;box-sizing:border-box;">{{ old('message') }}</textarea>
            </div>
            <div style="margin-bottom:20px;display:flex;justify-content:center;">
                <div class="h-captcha" data-sitekey="{{ config('hcaptcha.sitekey') }}" data-theme="{{ session('sh-theme', 'dark') }}"></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">{{ __('contact.form_submit') }}</button>
        </form>
    </div>
</section>
<footer>
    <a href="{{ $localeService->urlForLocale($currentLocale, '/') }}" class="logo">{{ config('app.name') }}</a>
    <p>&copy; 2011&ndash;{{ date('Y') }} {{ config('app.name') }}. {{ __('common.all_rights_reserved') }}</p>
</footer>
<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);})();
document.getElementById('themeToggle').addEventListener('click',function(){
    var c=document.documentElement.getAttribute('data-theme');
    var n=c==='dark'?'light':'dark';
    document.documentElement.setAttribute('data-theme',n);
    localStorage.setItem('sh-theme',n);
});
document.getElementById('navBurger').addEventListener('click',function(){
    document.getElementById('navMobile').classList.toggle('open');
});
function closeMobile(){document.getElementById('navMobile').classList.remove('open');}
</script>
</body>
</html>
