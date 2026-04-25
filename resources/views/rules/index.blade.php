<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'en' ? 'en' : 'uk' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('rules.title') }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('rules.subtitle') }}">
    <meta property="og:title" content="{{ __('rules.title') }} — {{ config('app.name') }}">
    <meta property="og:description" content="{{ __('rules.subtitle') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/rules') }}">
    <meta property="og:image" content="{{ url('/images/og-home.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ url('/images/og-home.png') }}">
    <link rel="canonical" href="{{ url('/rules') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
@include('components.nav-landing', ['landing' => false])

<main style="max-width:800px;margin:0 auto;padding:80px 24px 40px;box-sizing:border-box;">
    <h1 style="font-size:28px;font-weight:800;margin-bottom:8px;">{{ __('rules.title') }}</h1>
    <p style="color:var(--muted);margin-bottom:32px;font-size:14px;">{{ __('rules.subtitle') }}</p>

    @if($rules->isEmpty())
        <p style="color:var(--muted);text-align:center;padding:60px 0;">{{ __('rules.empty') }}</p>
    @else
    <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach($rules as $rule)
        <a href="{{ route('rules.show', $rule->slug) }}" style="text-decoration:none;">
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:border-color .2s;" onmouseover="this.style.borderColor='#eab308'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="display:flex;align-items:center;gap:14px;min-width:0;">
                    <span style="width:28px;height:28px;border-radius:50%;background:var(--bg-section);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--muted);flex-shrink:0;">{{ $loop->iteration }}</span>
                    <span style="font-size:15px;font-weight:600;color:var(--text);">{{ $rule->title() }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                    @if($rule->comments_count > 0)
                    <span style="display:flex;align-items:center;gap:4px;font-size:12px;color:var(--muted);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                        {{ $rule->comments_count }}
                    </span>
                    @endif
                    <svg width="16" height="16" fill="none" stroke="#eab308" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</main>
</body>
</html>
