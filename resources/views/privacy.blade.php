<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.privacy_title') }} — {{ config('app.name') }}</title>
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
        <a href="/" style="font-size:13px;color:var(--muted);text-decoration:none">&larr; {{ __('auth.back_home') }}</a>
    </div>
</nav>

<div style="min-height:100vh;padding:100px 20px 60px;background:var(--bg)">
<div style="max-width:760px;margin:0 auto">
    <h1 style="font-size:32px;font-weight:800;margin-bottom:8px">{{ __('auth.privacy_title') }}</h1>
    <p style="color:var(--muted);font-size:14px;margin-bottom:40px">{{ __('auth.privacy_updated') }}: 2025-01-01</p>

    @foreach([
        ['privacy_s1_title', 'privacy_s1_body'],
        ['privacy_s2_title', 'privacy_s2_body'],
        ['privacy_s3_title', 'privacy_s3_body'],
        ['privacy_s4_title', 'privacy_s4_body'],
        ['privacy_s5_title', 'privacy_s5_body'],
        ['privacy_s6_title', 'privacy_s6_body'],
    ] as [$title, $body])
    <div style="margin-bottom:32px">
        <h2 style="font-size:18px;font-weight:700;margin-bottom:10px;color:var(--text)">{{ __('auth.'.$title) }}</h2>
        <p style="font-size:15px;color:var(--muted);line-height:1.8">{!! __('auth.'.$body) !!}</p>
    </div>
    @endforeach
</div>
</div>
<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);})();
</script>
</body>
</html>
