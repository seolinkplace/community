@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Статтю апровнуто</h2>
<p>Вебмастер схвалив вашу статтю для розміщення на сайті <strong>{{ $domain }}</strong>.</p>
<a href="{{ config('app.url') }}/app/articles" class="btn">Мої статті</a>
@else
<h2>Article approved</h2>
<p>The webmaster approved your article for placement on <strong>{{ $domain }}</strong>.</p>
<a href="{{ config('app.url') }}/app/articles" class="btn">My articles</a>
@endif
@endsection