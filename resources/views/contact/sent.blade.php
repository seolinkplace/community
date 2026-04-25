<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __("contact.sent_title_page") }} — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="/css/landing.css">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YJ6ZE1CCFQ"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-YJ6ZE1CCFQ');</script>
</head>
<body>
<nav>
    <div class="nav-left">
        <a href="/" class="logo">{{ config('app.name') }}</a>
        <div class="nav-links">
            <a href="/#how">Як це працює</a>
            <a href="/#for-who">Для кого</a>
        </div>
    </div>
    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle" title="Змінити тему" aria-label="Змінити тему">
            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <a href="{{ route('unified.login') }}" class="nav-login">Увійти</a>
        <a href="{{ route('unified.register') }}" class="nav-register">Реєстрація</a>
        <a href="/en/contact" class="nav-lang">EN</a>
    </div>
    <button class="nav-burger" id="navBurger" aria-label="Меню">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
</nav>
<div class="nav-mobile" id="navMobile">
    <a href="/#how" onclick="closeMobile()">Як це працює</a>
    <a href="/#for-who" onclick="closeMobile()">Для кого</a>
    <a href="{{ route('unified.login') }}">Увійти</a>
    <a href="/en/contact">English</a>
</div>
<section style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:60px 20px;">
    <div style="width:100%;max-width:520px;text-align:center;">
        <div style="width:56px;height:56px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <svg width="24" height="24" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h1 style="font-size:24px;font-weight:800;margin-bottom:12px;">{{ __("contact.sent_title", ["name" => $contact->name]) }}</h1>
        <p style="color:var(--text-muted);margin-bottom:32px;font-size:15px;">{{ __("contact.sent_desc") }}</p>
        <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:32px;text-align:left;">
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;">{{ __("contact.reply_link_label") }}</p>
            <a href="{{ route('contact.reply', $contact->token) }}" style="color:var(--accent);font-size:14px;word-break:break-all;">
                {{ route('contact.reply', $contact->token) }}
            </a>
        </div>
        <a href="/" style="color:var(--text-muted);font-size:14px;">← {{ __("contact.back_home") }}</a>
    </div>
</section>
<footer>
    <a href="/" class="logo">{{ config('app.name') }}</a>
    <p>&copy; 2011&ndash;{{ date('Y') }} {{ config('app.name') }}. Всі права захищені.</p>
    <div class="footer-links"><a href="/en/contact">English</a></div>
</footer>
<script>
(function(){var s=localStorage.getItem('sh-theme')||'dark';document.documentElement.setAttribute('data-theme',s);})();
document.getElementById('themeToggle').addEventListener('click',function(){
    var c=document.documentElement.getAttribute('data-theme');
    var n=c==='dark'?'light':'dark';
    document.documentElement.setAttribute('data-theme',n);
    localStorage.setItem('sh-theme',n);
});
document.getElementById('navBurger').addEventListener('click',function(){
    document.getElementById('navMobile').classList.toggle('open');
});
function closeMobile(){document.getElementById('navMobile').classList.remove('open');}
</script>
</body></html>
