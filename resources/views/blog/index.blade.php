@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
    $allLocales    = $localeService->all();
    $baseUrl       = rtrim(config('app.url'), '/');
    $canonicalUrl  = $baseUrl . $localeService->urlForLocale($currentLocale, '/blog');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.blog_title') }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('common.blog_desc') }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($allLocales as $code => $config)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $baseUrl . $localeService->urlForLocale($code, '/blog') }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/blog">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ __('common.blog_title') }} — {{ config('app.name') }}">
    <meta property="og:description" content="{{ __('common.blog_desc') }}">
    <meta property="og:image" content="{{ $baseUrl }}/images/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $baseUrl }}/images/og-home.png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
@include('components.nav-landing', ['landing' => false])
<main style="max-width:1400px;margin:0 auto;padding:40px;box-sizing:border-box;">
    <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">{{ __('common.blog_title') }}</h1>
    <p style="color:var(--muted);margin-bottom:40px;">{{ __('common.blog_desc') }}</p>
    @if($posts->isEmpty())
        <p style="color:var(--muted);text-align:center;padding:60px 0;">{{ __('common.blog_empty') }}</p>
    @else
        <div class="blog-grid">
            @foreach($posts as $post)
                <a href="{{ $localeService->urlForLocale($currentLocale, '/blog/' . $post->slug) }}" style="text-decoration:none;">
                    <article class="blog-card">
                        @if($post->cover_image)
                            <img src="{{ $post->cover_image }}" alt="{{ $post->getTitle() }}" class="blog-cover-full">
                        @else
                            <div class="blog-cover-placeholder">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#eab308" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7V3m0 0L9 6m3-3 3 3M5 10h14M5 10a2 2 0 00-2 2v7a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2M5 10V7a2 2 0 012-2h10a2 2 0 012 2v3"/>
                                </svg>
                            </div>
                        @endif
                        <div class="blog-card-body">
                            <div class="blog-card-date">{{ $post->published_at->format('d.m.Y') }}</div>
                            <div class="blog-card-title">{{ $post->getTitle() }}</div>
                            @if($post->getExcerpt())
                                <div class="blog-card-excerpt">{{ \Str::limit($post->getExcerpt(), 120) }}</div>
                            @endif
                            <span class="blog-card-link">{{ __('common.read_more') }}</span>
                        </div>
                    </article>
                </a>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $posts->links() }}</div>
    @endif
</main>
@include('components.footer-landing')
<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);})();
document.getElementById('themeToggle').addEventListener('click',function(){var c=document.documentElement.getAttribute('data-theme');var n=c==='dark'?'light':'dark';document.documentElement.setAttribute('data-theme',n);localStorage.setItem('sh-theme',n);});
document.getElementById('navBurger').addEventListener('click',function(){document.getElementById('navMobile').classList.toggle('open');});
function closeMobile(){document.getElementById('navMobile').classList.remove('open');}
</script>
</body>
</html>
