<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f4f4f5;padding:32px 16px;}
        .wrap{max-width:540px;margin:0 auto;}
        .card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08);}
        .header{background:#111;padding:20px 28px;display:flex;align-items:center;}
        .logo{font-size:18px;font-weight:800;color:#fff;text-decoration:none;letter-spacing:-.5px;}
        .logo span{color:#eab308;}
        .body{padding:28px;color:#374151;font-size:15px;line-height:1.6;}
        .body h2{font-size:18px;font-weight:700;color:#111;margin-bottom:12px;}
        .body p{margin-bottom:12px;}
        .meta{background:#f9fafb;border-radius:8px;padding:14px 16px;margin:16px 0;font-size:14px;}
        .meta div{margin-bottom:4px;color:#6b7280;}
        .meta strong{color:#111;}
        .btn{display:inline-block;background:#111;color:#fff !important;padding:11px 22px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;margin-top:8px;}
        .footer{padding:16px 28px;font-size:12px;color:#9ca3af;border-top:1px solid #f3f4f6;}
        .footer a{color:#9ca3af;}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="header">
            <a href="{{ config('app.url') }}" class="logo">{{ config('app.name') }}</a>
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} &nbsp;·&nbsp; <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
        </div>
    </div>
</div>
</body>
</html>