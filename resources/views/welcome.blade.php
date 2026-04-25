@php
    use App\Services\LocaleService;
    $localeService = app(LocaleService::class);
    $currentLocale = App::getLocale();
    $allLocales    = $localeService->all();
    $baseUrl       = rtrim(config('app.url'), '/');
    $canonicalUrl  = $baseUrl . $localeService->urlForLocale($currentLocale, '/');
    $ogImage       = $baseUrl . '/images/og-home.png';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.landing_title') }}</title>
    <meta name="description" content="{{ __('common.landing_desc') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($allLocales as $code => $config)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $baseUrl . $localeService->urlForLocale($code, '/') }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ __('common.landing_og_title') }}">
    <meta property="og:description" content="{{ __('common.landing_og_desc') }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <link rel="stylesheet" href="/css/fonts.css">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YJ6ZE1CCFQ"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-YJ6ZE1CCFQ');</script>
</head>
<body>

@include('components.nav-landing', ['landing' => true])

{{-- ─── Hero ─────────────────────────────────────────────────────────────── --}}
<section class="hero">
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>
    <div class="hero-inner">
        <div>
            <div class="badge"><span class="badge-dot"></span>{{ __('landing.badge') }}</div>
            <h1>{{ __('landing.hero_h1_1') }}<br><span class="ac">{{ __('landing.hero_h1_2') }}</span> {{ __('landing.hero_h1_3') }}<br><span class="ac2">{{ __('landing.hero_h1_4') }}</span></h1>
            <p class="hero-desc">{{ __('landing.hero_desc') }}</p>
            <div class="hero-actions">
                <a href="{{ route('unified.register') }}" class="btn btn-primary">{{ __('landing.hero_btn_register') }}</a>
                <a href="#how" class="btn btn-outline">{{ __('landing.hero_btn_how') }}</a>
            </div>
        </div>
        <div class="stats">
            <div class="stat">
                <div class="stat-body"><div class="stat-n">{{ __('landing.stat_1_n') }}</div><div class="stat-l">{{ __('landing.stat_1_l') }}</div></div>
                <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
            </div>
            <div class="stat">
                <div class="stat-body"><div class="stat-n">{{ __('landing.stat_2_n') }}</div><div class="stat-l">{{ __('landing.stat_2_l') }}</div></div>
                <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
            </div>
            <div class="stat">
                <div class="stat-body"><div class="stat-n">{{ __('landing.stat_3_n') }}</div><div class="stat-l">{{ __('landing.stat_3_l') }}</div></div>
                <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
            </div>
            <div class="stat">
                <div class="stat-body"><div class="stat-n">{{ __('landing.stat_4_n') }}</div><div class="stat-l">{{ __('landing.stat_4_l') }}</div></div>
                <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
        </div>
    </div>
</section>

{{-- ─── How it works ─────────────────────────────────────────────────────── --}}
<section id="how" class="section-alt">
    <div class="container">
        <div class="s-label">{{ __('landing.how_label') }}</div>
        <h2>{{ __('landing.how_h2') }}</h2>
        <p class="s-desc">{{ __('landing.how_desc') }}</p>
        <div class="grid3">
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.how_1_n') }}</div>
                <div class="card-t">{{ __('landing.how_1_t') }}</div>
                <div class="card-d">{{ __('landing.how_1_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.how_2_n') }}</div>
                <div class="card-t">{{ __('landing.how_2_t') }}</div>
                <div class="card-d">{{ __('landing.how_2_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.how_3_n') }}</div>
                <div class="card-t">{{ __('landing.how_3_t') }}</div>
                <div class="card-d">{{ __('landing.how_3_d') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ─── For who ──────────────────────────────────────────────────────────── --}}
<section id="for-who">
    <div class="container">
        <div class="s-label">{{ __('landing.forwho_label') }}</div>
        <h2>{{ __('landing.forwho_h2') }}</h2>
        <p class="s-desc">{{ __('landing.forwho_desc') }}</p>
        <div class="grid2">
            <div class="fw-card wm fade-up">
                <div class="fw-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                <div class="fw-t">{{ __('landing.wm_t') }}</div>
                <div class="fw-d">{{ __('landing.wm_d') }}</div>
                <ul class="flist">
                    @foreach(['wm_f1','wm_f2','wm_f3','wm_f4','wm_f5','wm_f6'] as $k)
                    <li><span class="check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('landing.'.$k) }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('unified.register') }}" class="btn btn-primary">{{ __('landing.wm_btn') }}</a>
            </div>
            <div class="fw-card biz fade-up">
                <div class="fw-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                <div class="fw-t">{{ __('landing.biz_t') }}</div>
                <div class="fw-d">{{ __('landing.biz_d') }}</div>
                <ul class="flist">
                    @foreach(['biz_f1','biz_f2','biz_f3','biz_f4','biz_f5','biz_f6'] as $k)
                    <li><span class="check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('landing.'.$k) }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('unified.register') }}" class="btn btn-pink">{{ __('landing.biz_btn') }}</a>
            </div>
        </div>
    </div>
</section>

{{-- ─── Placement types ──────────────────────────────────────────────────── --}}
<section class="section-alt">
    <div class="container">
        <div class="s-label">{{ __('landing.types_label') }}</div>
        <h2>{{ __('landing.types_h2') }}</h2>
        <p class="s-desc">{{ __('landing.types_desc') }}</p>
        <div class="grid3">
            <div class="card fade-up">
                <div class="type-tag tag-link">LINK</div>
                <div class="type-t">{{ __('landing.type_link_t') }}</div>
                <div class="type-d">{{ __('landing.type_link_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="type-tag tag-onclick">ONCLICK</div>
                <div class="type-t">{{ __('landing.type_onclick_t') }}</div>
                <div class="type-d">{{ __('landing.type_onclick_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="type-tag tag-article">ARTICLE</div>
                <div class="type-t">{{ __('landing.type_article_t') }}</div>
                <div class="type-d">{{ __('landing.type_article_d') }}</div>
            </div>
        </div>
        <div class="pricing-note">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('landing.pricing_note') }}
        </div>
    </div>
</section>

{{-- ─── Tasks ────────────────────────────────────────────────────────────── --}}
<section id="tasks">
    <div class="container">
        <div class="s-label">{{ __('landing.tasks_label') }}</div>
        <h2>{{ __('landing.tasks_h2_1') }}<br><span class="ac">{{ __('landing.tasks_h2_2') }}</span></h2>
        <p class="s-desc">{{ __('landing.tasks_desc') }}</p>
        <div class="grid2" style="margin-top:40px">
            <div class="fw-card wm fade-up">
                <div class="fw-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="fw-t">{{ __('landing.performer_t') }}</div>
                <div class="fw-d">{{ __('landing.performer_d') }}</div>
                <ul class="flist">
                    @foreach(['performer_f1','performer_f2','performer_f3','performer_f4','performer_f5'] as $k)
                    <li><span class="check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('landing.'.$k) }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('unified.register') }}" class="btn btn-primary">{{ __('landing.performer_btn') }}</a>
            </div>
            <div class="fw-card biz fade-up">
                <div class="fw-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/><path d="M17.5 3.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 7.5-7.5z"/></svg></div>
                <div class="fw-t">{{ __('landing.advertiser_t') }}</div>
                <div class="fw-d">{{ __('landing.advertiser_d') }}</div>
                <ul class="flist">
                    @foreach(['advertiser_f1','advertiser_f2','advertiser_f3','advertiser_f4','advertiser_f5'] as $k)
                    <li><span class="check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('landing.'.$k) }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('unified.register') }}" class="btn btn-pink">{{ __('landing.advertiser_btn') }}</a>
            </div>
        </div>
        <div class="pricing-note">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('landing.tasks_note') }}
        </div>
    </div>
</section>

{{-- ─── Referral ─────────────────────────────────────────────────────────── --}}
<section id="referral" class="section-alt">
    <div class="container">
        <div class="s-label">{{ __('landing.ref_label') }}</div>
        <h2>{{ __('landing.ref_h2_1') }}<br><span class="ac">{{ __('landing.ref_h2_2') }}</span></h2>
        <p class="s-desc">{{ __('landing.ref_desc') }}</p>
        <div class="grid3">
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.ref_1_n') }}</div>
                <div class="card-t">{{ __('landing.ref_1_t') }}</div>
                <div class="card-d">{{ __('landing.ref_1_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.ref_2_n') }}</div>
                <div class="card-t">{{ __('landing.ref_2_t') }}</div>
                <div class="card-d">{{ __('landing.ref_2_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="card-n">{{ __('landing.ref_3_n') }}</div>
                <div class="card-t">{{ __('landing.ref_3_t') }}</div>
                <div class="card-d">{{ __('landing.ref_3_d') }}</div>
            </div>
        </div>
        <div class="pricing-note">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('landing.ref_note') }}
        </div>
    </div>
</section>

{{-- ─── Integration ──────────────────────────────────────────────────────── --}}
<section id="integration">
    <div class="container">
        <div class="s-label">{{ __('landing.integration_label') }}</div>
        <h2>{{ __('landing.integration_h2_1') }}<br><span class="ac2">{{ __('landing.integration_h2_2') }}</span></h2>
        <p class="s-desc">{{ __('landing.integration_desc') }}</p>
        <div class="grid3">
            <div class="card fade-up">
                <div class="type-tag tag-link">WP</div>
                <div class="type-t">{{ __('landing.int_wp_t') }}</div>
                <div class="type-d">{{ __('landing.int_wp_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="type-tag tag-onclick">PHP</div>
                <div class="type-t">{{ __('landing.int_php_t') }}</div>
                <div class="type-d">{{ __('landing.int_php_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="type-tag tag-article">API</div>
                <div class="type-t">{{ __('landing.int_api_t') }}</div>
                <div class="type-d">{{ __('landing.int_api_d') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ─── Trust ────────────────────────────────────────────────────────────── --}}
<section class="section-alt">
    <div class="container">
        <div class="s-label">{{ __('landing.trust_label') }}</div>
        <h2>{{ __('landing.trust_h2') }}</h2>
        <p class="s-desc">{{ __('landing.trust_desc') }}</p>
        <div class="grid2">
            <div class="card fade-up">
                <div class="fw-icon" style="margin-bottom:16px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <div class="type-t">{{ __('landing.trust_1_t') }}</div>
                <div class="type-d">{{ __('landing.trust_1_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="fw-icon" style="margin-bottom:16px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
                <div class="type-t">{{ __('landing.trust_2_t') }}</div>
                <div class="type-d">{{ __('landing.trust_2_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="fw-icon" style="margin-bottom:16px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
                <div class="type-t">{{ __('landing.trust_3_t') }}</div>
                <div class="type-d">{{ __('landing.trust_3_d') }}</div>
            </div>
            <div class="card fade-up">
                <div class="fw-icon" style="margin-bottom:16px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
                <div class="type-t">{{ __('landing.trust_4_t') }}</div>
                <div class="type-d">{{ __('landing.trust_4_d') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ─── FAQ ──────────────────────────────────────────────────────────────── --}}
<section id="faq">
    <div class="container">
        <div class="s-label">{{ __('landing.faq_label') }}</div>
        <h2>{{ __('landing.faq_h2') }}</h2>
        <p class="s-desc">{{ __('landing.faq_desc') }}</p>
        <div class="faq-list">
            @foreach(range(1,8) as $i)
            <div class="faq-item fade-up">
                <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                    {{ __('landing.faq_'.$i.'_q') }}
                    <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a">{{ __('landing.faq_'.$i.'_a') }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CTA ──────────────────────────────────────────────────────────────── --}}
<section class="section-alt">
    <div class="container" style="text-align:center;padding:60px 20px">
        <div class="s-label">{{ __('landing.cta_label') }}</div>
        <h2>{{ __('landing.cta_h2') }}</h2>
        <p class="s-desc" style="max-width:480px;margin:0 auto 32px">{{ __('landing.cta_desc') }}</p>
        <div class="hero-actions" style="justify-content:center">
            <a href="{{ route('unified.register') }}" class="btn btn-primary">{{ __('landing.cta_btn_register') }}</a>
            <a href="{{ route('unified.login') }}" class="btn btn-outline">{{ __('landing.cta_btn_login') }}</a>
        </div>
    </div>
</section>

@include('components.footer-landing')

<script>
(function(){
    var s = localStorage.getItem('sh-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', s);
})();
document.getElementById('themeToggle').addEventListener('click', function() {
    var c = document.documentElement.getAttribute('data-theme');
    var n = c === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', n);
    localStorage.setItem('sh-theme', n);
});
document.getElementById('navBurger').addEventListener('click', function() {
    document.getElementById('navMobile').classList.toggle('open');
});
function closeMobile() {
    document.getElementById('navMobile').classList.remove('open');
}
var obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(e, i) {
        if (e.isIntersecting) {
            setTimeout(function() { e.target.classList.add('visible'); }, i * 100);
            obs.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-up').forEach(function(el) { obs.observe(el); });
</script>
</body>
</html>
