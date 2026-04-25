<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.role_select_title') }} — {{ config('app.name') }}</title>
    <link rel="canonical" href="{{ config('app.url') }}/">
    <link rel="alternate" hreflang="uk" href="{{ config('app.url') }}/">
    <link rel="alternate" hreflang="en" href="{{ config('app.url') }}/en/">
    <link rel="alternate" hreflang="x-default" href="{{ config('app.url') }}/">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}/">
    <meta property="og:title" content="{{ config('app.name') }} — Маркетплейс SEO-розміщень">
    <meta property="og:description" content="Платформа де вебмайстри монетизують сайти, а бізнес отримує якісні SEO-посилання та статті.">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-home.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    <style>
        .role-card { display:block; position:relative; background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:14px 16px 14px 50px; cursor:pointer; transition:border-color .2s; box-sizing:border-box; }
        .role-card input[type="radio"] { position:absolute; opacity:0; width:0; height:0; }
        .role-card .radio-dot { position:absolute; left:16px; top:50%; transform:translateY(-50%); width:20px; height:20px; border-radius:50%; border:2px solid var(--border); background:transparent; transition:border-color .2s; flex-shrink:0; }
        .role-card .radio-dot::after { content:''; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%) scale(0); width:10px; height:10px; border-radius:50%; background:var(--accent); transition:transform .15s; }
        .role-card input[type="radio"]:checked ~ .radio-dot { border-color:var(--accent); }
        .role-card input[type="radio"]:checked ~ .radio-dot::after { transform:translate(-50%,-50%) scale(1); }
        .role-card:has(input:checked) { border-color:var(--accent); }
        .role-card .card-title { font-size:14px; font-weight:600; color:var(--text); margin-bottom:3px; }
        .role-card .card-desc { font-size:13px; color:var(--muted); line-height:1.5; }
    </style>
</head>
<body>
<nav>
    <div class="nav-left">
        <a href="/" class="logo">{{ config('app.name') }}</a>
    </div>
    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle" aria-label="theme">
            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        @if(app()->getLocale() === 'uk')
            <a href="{{ route('lang.switch', 'en') }}" class="nav-lang">EN</a>
        @else
            <a href="{{ route('lang.switch', 'uk') }}" class="nav-lang">UK</a>
        @endif
    </div>
</nav>

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:80px 20px 40px;background:var(--bg)">
    <div style="width:100%;max-width:560px">

        <div style="margin-bottom:28px">
            <div class="s-label">{{ __('auth.account_label') }}</div>
            <h2 style="margin-bottom:6px">{{ __('auth.role_select_title') }}</h2>
            <p class="s-desc" style="max-width:100%">{{ __('auth.role_select_sub') }}</p>
        </div>

        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:20px;padding:36px">

            @if($errors->any())
            <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f87171">
                @foreach($errors->all() as $e)<p style="margin:0">{{ $e }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('unified.google.role.store') }}">
                @csrf

                <div style="margin-bottom:24px">
                    <div style="font-size:13px;font-weight:500;color:var(--muted);margin-bottom:12px">{{ __('auth.role_label') }}</div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        @foreach([
                            ['client',    'auth.role_client',    'auth.role_client_desc'],
                            ['webmaster', 'auth.role_webmaster', 'auth.role_webmaster_desc'],
                            ['performer', 'auth.role_performer', 'auth.role_performer_desc'],
                        ] as [$val, $lbl, $desc])
                        <label class="role-card">
                            <input type="radio" name="role" value="{{ $val }}" {{ $val === 'client' ? 'checked' : '' }}>
                            <span class="radio-dot"></span>
                            <div class="card-title">{{ __($lbl) }}</div>
                            <div class="card-desc">{{ __($desc) }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom:24px">
                    <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px">
                        {{ __('auth.ref_code') }} <span style="font-weight:400">({{ __('auth.optional') }})</span>
                    </label>
                    <input type="text" name="ref_code" value="{{ old('ref_code', request('ref')) }}" placeholder="XXXXXXXX"
                           style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>

                <button type="submit"
                    style="width:100%;padding:12px;border-radius:12px;font-size:15px;font-weight:600;background:var(--accent);color:#0c0c0c;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;margin-top:4px">
                    {{ __('auth.btn_continue') }}
                </button>
            </form>
        </div>

        <p style="text-align:center;font-size:12px;color:var(--muted);margin-top:20px">
            <a href="/" style="color:var(--muted);text-decoration:none">&larr; {{ __('auth.back_home') }}</a>
        </p>
    </div>
</div>

<script>
(function(){
    var s = localStorage.getItem('sh-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', s);
})();
document.getElementById('themeToggle').addEventListener('click', function(){
    var c = document.documentElement.getAttribute('data-theme');
    var n = c === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', n);
    localStorage.setItem('sh-theme', n);
});
</script>
</body>
</html>
