@extends('emails.layout')
@section('content')
@if($locale === 'uk')
<h2>Нове виконання завдання</h2>
<p>Користувач виконав ваше завдання і чекає на перевірку.</p>
<div class="meta">
    <div>Завдання: <strong>{{ $taskTitle }}</strong></div>
    <div>Виконавець: <strong>{{ $performerName }}</strong></div>
</div>
<a href="{{ $taskUrl }}" class="btn">Переглянути виконання</a>
@else
<h2>New task completion</h2>
<p>A user has completed your task and is awaiting review.</p>
<div class="meta">
    <div>Task: <strong>{{ $taskTitle }}</strong></div>
    <div>Performer: <strong>{{ $performerName }}</strong></div>
</div>
<a href="{{ $taskUrl }}" class="btn">Review completion</a>
@endif
@endsection
