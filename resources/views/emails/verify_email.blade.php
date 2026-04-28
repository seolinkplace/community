@extends('emails.layout')

@section('content')
<h2 style="margin:0 0 16px;">{{ __('auth.verify_email_title', [], $locale) }}</h2>
<p style="margin:0 0 16px;">{{ __('auth.verify_email_greeting', ['name' => $name], $locale) }}</p>
<p style="margin:0 0 24px;">{{ __('auth.verify_email_body', [], $locale) }}</p>
<p style="text-align:center;margin:0 0 24px;">
    <a href="{{ $url }}"
       style="display:inline-block;background:#eab308;color:#000;font-weight:700;padding:14px 32px;border-radius:8px;text-decoration:none;font-size:16px;">
        {{ __('auth.verify_email_button', [], $locale) }}
    </a>
</p>
<p style="margin:0 0 8px;color:#6b7280;font-size:14px;">{{ __('auth.verify_email_expires', [], $locale) }}</p>
<p style="margin:0;color:#6b7280;font-size:13px;word-break:break-all;">{{ $url }}</p>
@endsection
