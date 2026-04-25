<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('common.error_404_title') }} — {{ config('app.name') }}</title>
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
        .code {
            font-family: 'Syne', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            color: #e2e8f0;
            line-height: 1;
            margin-bottom: 1rem;
        }
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
            margin-bottom: 1.5rem;
        }
        .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-primary { background: #0ea5e9; color: #fff; }
        .btn-primary:hover { background: #0284c7; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <h1>{{ __('common.error_404_title') }}</h1>
        <p>{{ __('common.error_404_desc') }}</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('common.error_go_home') }}</a>
            <a href="javascript:history.back()" class="btn btn-secondary">{{ __('common.error_go_back') }}</a>
        </div>
    </div>
</body>
</html>
