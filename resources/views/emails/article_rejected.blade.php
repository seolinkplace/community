@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Статтю відхилено</h2>
<p>Вебмастер відхилив вашу статтю для розміщення на сайті <strong>{{ $domain }}</strong>.</p>
@if($reason)<div class="meta"><div>Причина: <strong>{{ $reason }}</strong></div></div>@endif
<a href="{{ config('app.url') }}/app/articles" class="btn">Мої статті</a>
@else
<h2>Article rejected</h2>
<p>The webmaster rejected your article for placement on <strong>{{ $domain }}</strong>.</p>
@if($reason)<div class="meta"><div>Reason: <strong>{{ $reason }}</strong></div></div>@endif
<a href="{{ config('app.url') }}/app/articles" class="btn">My articles</a>
@endif
@endsection