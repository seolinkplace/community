<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'en' ? 'en' : 'uk' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $rule->title() }} — {{ __('rules.title') }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($rule->body()), 160) }}">
    <meta property="og:title" content="{{ $rule->title() }} — {{ config('app.name') }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($rule->body()), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url('/rules/' . $rule->slug) }}">
    <meta property="og:image" content="{{ url('/images/og-home.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ url('/images/og-home.png') }}">
    <link rel="canonical" href="{{ url('/rules/' . $rule->slug) }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .prose-rule h2{font-size:18px;font-weight:700;margin:24px 0 10px;color:var(--text);}
        .prose-rule h3{font-size:15px;font-weight:700;margin:18px 0 8px;color:var(--text);}
        .prose-rule p{font-size:14px;line-height:1.7;color:var(--muted);margin-bottom:12px;}
        .prose-rule ul,.prose-rule ol{padding-left:20px;margin-bottom:12px;}
        .prose-rule li{font-size:14px;line-height:1.7;color:var(--muted);margin-bottom:4px;}
        .prose-rule strong{color:var(--text);font-weight:600;}
        .prose-rule a{color:#eab308;}
        .comment-form textarea{width:100%;background:var(--bg-section);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:14px;color:var(--text);resize:vertical;min-height:80px;box-sizing:border-box;}
        .comment-form textarea:focus{outline:none;border-color:#eab308;}
        .btn-primary{background:#eab308;color:#000;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;}
        .btn-primary:hover{background:#ca9a00;}
        .btn-ghost{background:transparent;border:1px solid var(--border);color:var(--muted);padding:6px 14px;border-radius:8px;font-size:13px;cursor:pointer;}
        .btn-ghost:hover{border-color:var(--text);}
        @media(max-width:640px){main{padding:20px 16px!important;}}
    </style>
</head>
<body>
@include('components.nav-landing', ['landing' => false])

<main style="max-width:800px;margin:0 auto;padding:80px 24px 40px;box-sizing:border-box;">

    {{-- Breadcrumb --}}
    <nav style="font-size:13px;color:var(--muted);margin-bottom:24px;display:flex;align-items:center;gap:8px;">
        <a href="{{ route('rules.index') }}" style="color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#eab308'" onmouseout="this.style.color='var(--muted)'">{{ __('rules.title') }}</a>
        <span>/</span>
        <span style="color:var(--text);">{{ $rule->title() }}</span>
    </nav>

    {{-- Rule body --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:28px 28px 24px;margin-bottom:32px;">
        <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:20px;">{{ $rule->title() }}</h1>
        <div class="prose-rule">
            {!! $rule->body() !!}
        </div>
    </div>

    {{-- Comments section --}}
    <div>
        <h2 style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:20px;">
            {{ __('rules.comments') }}
            @if($comments->isNotEmpty())
            <span style="color:var(--muted);font-weight:400;font-size:14px;margin-left:6px;">({{ $comments->count() }})</span>
            @endif
        </h2>

        @if(session('success'))
        <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:10px 14px;font-size:13px;color:#22c55e;margin-bottom:16px;">
            {{ session('success') }}
        </div>
        @endif

        {{-- Comment form --}}
        @auth('unified')
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:20px;" class="comment-form">
            <form method="POST" action="{{ route('rules.comment', $rule->slug) }}">
                @csrf
                <textarea name="body" placeholder="{{ __('rules.comment_placeholder') }}" required minlength="3" maxlength="2000">{{ old('body') }}</textarea>
                @error('body')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                <div style="margin-top:10px;display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn-primary">{{ __('rules.comment_submit') }}</button>
                </div>
            </form>
        </div>
        @else
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:14px;text-align:center;font-size:14px;color:var(--muted);margin-bottom:20px;">
            <a href="{{ route('unified.login') }}" style="color:#eab308;text-decoration:none;">{{ __('rules.login_to_comment') }}</a>
        </div>
        @endauth

        {{-- Comments list --}}
        @if($comments->isEmpty())
        <p style="text-align:center;color:var(--muted);font-size:14px;padding:30px 0;">{{ __('rules.no_comments') }}</p>
        @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($comments as $comment)
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:16px;" id="comment-{{ $comment->id }}">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <span style="font-size:14px;font-weight:600;color:var(--text);">{{ $comment->user->name }}</span>
                    <span style="font-size:12px;color:var(--muted);">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-size:14px;color:var(--muted);line-height:1.6;margin:0;white-space:pre-line;">{{ $comment->body }}</p>

                @auth('unified')
                <div style="margin-top:8px;">
                    <button onclick="toggleReply({{ $comment->id }})" style="font-size:12px;color:var(--muted);background:none;border:none;cursor:pointer;padding:0;" onmouseover="this.style.color='#eab308'" onmouseout="this.style.color='var(--muted)'">
                        {{ __('rules.reply') }}
                    </button>
                </div>
                <div id="reply-form-{{ $comment->id }}" style="display:none;margin-top:12px;" class="comment-form">
                    <form method="POST" action="{{ route('rules.comment', $rule->slug) }}">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="body" placeholder="{{ __('rules.reply_placeholder') }}" required minlength="3" maxlength="2000" style="min-height:60px;"></textarea>
                        <div style="margin-top:8px;display:flex;gap:8px;justify-content:flex-end;">
                            <button type="button" onclick="toggleReply({{ $comment->id }})" class="btn-ghost">{{ __('rules.cancel') }}</button>
                            <button type="submit" class="btn-primary">{{ __('rules.reply_submit') }}</button>
                        </div>
                    </form>
                </div>
                @endauth

                {{-- Replies --}}
                @if($comment->replies->isNotEmpty())
                <div style="margin-top:12px;margin-left:16px;padding-left:16px;border-left:2px solid var(--border);display:flex;flex-direction:column;gap:10px;">
                    @foreach($comment->replies as $reply)
                    <div id="comment-{{ $reply->id }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $reply->user->name }}</span>
                            <span style="font-size:12px;color:var(--muted);">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="font-size:13px;color:var(--muted);line-height:1.6;margin:0;white-space:pre-line;">{{ $reply->body }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div style="margin-top:32px;text-align:center;">
        <a href="{{ route('rules.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#eab308'" onmouseout="this.style.color='var(--muted)'">← {{ __('rules.back') }}</a>
    </div>
</main>

<script>
function toggleReply(id) {
    const el = document.getElementById('reply-form-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
