@extends('webmaster.layouts.app')
@section('title', 'Чат')
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('webmaster.orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Замовлення</a>
        <span class="text-gray-300">/</span>
        <span class="text-sm font-semibold text-gray-900">Чат</span>
    </div>

    {{-- Контекст --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4 text-xs text-gray-600 space-y-1">
        <div class="flex items-center gap-6 flex-wrap">
            <span>Кампанія: <strong class="text-gray-800">{{ $campaignLink->campaign?->name ?? '—' }}</strong></span>
            <span>Анкор: <strong class="text-gray-800">{{ $campaignLink->anchor ?: '—' }}</strong></span>
            @if($campaignLink->donor_url)
            <a href="{{ $campaignLink->donor_url }}?t={{ time() }}" target="_blank"
               class="text-blue-500 hover:underline">Переглянути сторінку</a>
            @endif
        </div>
    </div>

    @include('_chat_window', [
        'senderType' => 'webmaster',
        'pollUrl'    => route('webmaster.chat.poll', $campaignLink),
        'sendUrl'    => route('webmaster.chat.send', $campaignLink),
    ])
</div>
@endsection
