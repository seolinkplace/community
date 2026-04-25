@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Нове замовлення</h2>
<p>На вашому сайті створено нове замовлення і чекає на ваш розгляд.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Тип: <strong>{{ $placementType }}</strong></div>
    <div>Ціна: <strong>${{ $pricePerDay }}/день</strong></div>
</div>
<a href="{{ config('app.url') }}/wm/orders" class="btn">Переглянути замовлення</a>
@else
<h2>New order</h2>
<p>A new order has been placed on your site and is waiting for your review.</p>
<div class="meta">
    <div><strong>{{ $domain }}</strong></div>
    <div>Type: <strong>{{ $placementType }}</strong></div>
    <div>Price: <strong>${{ $pricePerDay }}/day</strong></div>
</div>
<a href="{{ config('app.url') }}/wm/orders" class="btn">View order</a>
@endif
@endsection