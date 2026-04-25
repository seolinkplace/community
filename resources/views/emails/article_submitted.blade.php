@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Нова стаття на перевірку</h2>
<p>Клієнт надіслав статтю для розміщення на вашому сайті.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Тема: <strong>{{ $topic }}</strong></div>
</div>
<a href="{{ config('app.url') }}/wm/articles" class="btn">Переглянути статтю</a>
@else
<h2>New article for review</h2>
<p>A client submitted an article for placement on your site.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Topic: <strong>{{ $topic }}</strong></div>
</div>
<a href="{{ config('app.url') }}/wm/articles" class="btn">View article</a>
@endif
@endsection