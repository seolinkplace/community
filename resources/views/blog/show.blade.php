@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
    $allLocales    = $localeService->all();
    $baseUrl       = rtrim(config('app.url'), '/');
    $canonicalUrl  = $baseUrl . $localeService->urlForLocale($currentLocale, '/blog/' . $post->slug);
    $ogImage       = $post->cover_image ? url($post->cover_image) : $baseUrl . '/images/og-home.png';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->getMetaTitle() }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ $post->getMetaDescription() }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($allLocales as $code => $config)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $baseUrl . $localeService->urlForLocale($code, '/blog/' . $post->slug) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/blog/{{ $post->slug }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $post->getMetaTitle() }}">
    <meta property="og:description" content="{{ $post->getMetaDescription() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
@include('components.nav-landing', ['landing' => false])
<div class="blog-article">
    <a href="{{ $localeService->urlForLocale($currentLocale, '/blog') }}" class="blog-back">
        &larr; {{ __('common.back_to_blog') }}
    </a>
    @if($post->title_en && $post->content_en)
        <div class="lang-switch">
            @foreach($localeService->primary() as $code => $config)
                <a href="{{ $localeService->urlForLocale($code, '/blog/' . $post->slug) }}"
                   class="lang-btn {{ $code === $currentLocale ? 'active' : '' }}">
                    {{ strtoupper($code) }}
                </a>
            @endforeach
        </div>
    @endif
    <div class="blog-date">{{ $post->published_at->format('d.m.Y') }}</div>
    <h1 class="blog-title">{{ $post->getTitle() }}</h1>
    @if($post->cover_image)
        <img src="{{ $post->cover_image }}" alt="{{ $post->getTitle() }}" class="blog-cover-full">
    @endif
    <div class="blog-content">
        {!! $post->getContent() !!}
    </div>
</div>
@include('components.footer-landing')
<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);})();
document.getElementById('themeToggle').addEventListener('click',function(){var c=document.documentElement.getAttribute('data-theme');var n=c==='dark'?'light':'dark';document.documentElement.setAttribute('data-theme',n);localStorage.setItem('sh-theme',n);});
document.getElementById('navBurger').addEventListener('click',function(){document.getElementById('navMobile').classList.toggle('open');});
function closeMobile(){document.getElementById('navMobile').classList.remove('open');}
</script>
</body>
</html>
