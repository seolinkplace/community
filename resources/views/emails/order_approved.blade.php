@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Замовлення апровнуто</h2>
<p>Вебмастер схвалив ваше замовлення.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Тип: <strong>{{ $placementType }}</strong></div>
    <div>Ціна: <strong>${{ $pricePerDay }}/день</strong></div>
</div>
<a href="{{ config('app.url') }}/app/orders" class="btn">Мої замовлення</a>
@else
<h2>Order approved</h2>
<p>The webmaster has approved your order.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Type: <strong>{{ $placementType }}</strong></div>
    <div>Price: <strong>${{ $pricePerDay }}/day</strong></div>
</div>
<a href="{{ config('app.url') }}/app/orders" class="btn">My orders</a>
@endif
@endsection