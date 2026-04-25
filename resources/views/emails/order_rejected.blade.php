@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Замовлення відхилено</h2>
<p>На жаль, вебмастер відхилив ваше замовлення на сайті <strong>{{ $domain }}</strong>.</p>
@if($reason)<div class="meta"><div>Причина: <strong>{{ $reason }}</strong></div></div>@endif
<a href="{{ config('app.url') }}/app/catalog" class="btn">Знайти інший сайт</a>
@else
<h2>Order rejected</h2>
<p>Unfortunately, the webmaster rejected your order on <strong>{{ $domain }}</strong>.</p>
@if($reason)<div class="meta"><div>Reason: <strong>{{ $reason }}</strong></div></div>@endif
<a href="{{ config('app.url') }}/app/catalog" class="btn">Find another site</a>
@endif
@endsection