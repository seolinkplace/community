<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('common.error_500_title') }} — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }
        .icon {
            width: 64px;
            height: 64px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon svg { width: 32px; height: 32px; color: #ef4444; }
        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #0f172a;
        }
        p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .uuid {
            background: #f1f5f9;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            color: #94a3b8;
            font-family: monospace;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }
        .btn {
            display: inline-block;
            background: #0ea5e9;
            color: #fff;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn:hover { background: #0284c7; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </div>
        <h1>{{ __('common.error_500_title') }}</h1>
        <p>{{ __('common.error_500_desc') }}</p>
        @if(!empty($uuid))
            <div class="uuid">{{ __('common.error_report_id') }}: {{ $uuid }}</div>
        @endif
        <a href="{{ url('/') }}" class="btn">{{ __('common.error_go_home') }}</a>
    </div>
</body>
</html>
