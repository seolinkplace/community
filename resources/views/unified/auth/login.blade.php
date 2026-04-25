<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login_title') }} — {{ config('app.name') }}</title>
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
    <div style="width:100%;max-width:420px">

        <div style="margin-bottom:28px">
            <div class="s-label">{{ __('auth.account_label') }}</div>
            <h2 style="margin-bottom:6px">{{ __('auth.login_title') }}</h2>
            <p class="s-desc" style="max-width:100%">{{ __('auth.login_sub') }}</p>
        </div>

        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:20px;padding:36px">

            @if($errors->any())
            <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f87171">
                @foreach($errors->all() as $e)<p style="margin:0">{{ $e }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('unified.login') }}">
                @csrf
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px">{{ __('auth.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div style="margin-bottom:8px">
                    <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px">{{ __('auth.password') }}</label>
                    <input type="password" name="password" required
                           style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--muted)">
                        <input type="checkbox" name="remember" style="width:auto;accent-color:var(--accent)">
                        {{ __('auth.remember_me') }}
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--muted);text-decoration:none">
                        {{ __('auth.forgot_password') }}
                    </a>
                    @endif
                </div>
                <div style="display:flex;justify-content:center;margin-bottom:16px"><div class="h-captcha" id="hcaptcha-widget" data-sitekey="{{ config('hcaptcha.sitekey') }}" data-theme="dark"></div></div>
                <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
                <button type="submit"
                    style="width:100%;padding:12px;border-radius:12px;font-size:15px;font-weight:600;background:var(--accent);color:#0c0c0c;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s">
                    {{ __('auth.btn_login') }}
                </button>
            </form>

            <div style="display:flex;align-items:center;gap:12px;margin-top:20px;margin-bottom:4px;">
                <div style="flex:1;height:1px;background:var(--border)"></div>
                <span style="font-size:12px;color:var(--muted)">{{ __('auth.or') }}</span>
                <div style="flex:1;height:1px;background:var(--border)"></div>
            </div>
            <a href="{{ route('auth.google') }}"
                style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:11px;border-radius:12px;font-size:14px;font-weight:600;background:transparent;border:1px solid var(--border);color:var(--text);text-decoration:none;transition:all .2s;margin-bottom:16px;">
                <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                {{ __('auth.google_signin') }}
            </a>
            <p style="text-align:center;font-size:13px;color:var(--muted);">
                {{ __('auth.no_account') }}
                <a href="{{ route('unified.register') }}" style="color:var(--text);text-decoration:none;font-weight:500">{{ __('auth.btn_register') }}</a>
            </p>
        </div>

        <p style="text-align:center;font-size:12px;color:var(--muted);margin-top:20px">
            <a href="/" style="color:var(--muted);text-decoration:none">&larr; {{ __('auth.back_home') }}</a>
        </p>
    </div>
</div>

<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);var w=document.getElementById('hcaptcha-widget');if(w)w.setAttribute('data-theme',s);})();
document.getElementById('themeToggle').addEventListener('click',function(){
    var c=document.documentElement.getAttribute('data-theme');
    var n=c==='dark'?'light':'dark';
    document.documentElement.setAttribute('data-theme',n);
    localStorage.setItem('sh-theme',n);
});
</script>
</body>
</html>