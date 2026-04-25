@extends('client.layouts.app')
@section('title', 'Чат')
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('client.chat.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Чати</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('client.campaigns.show', $campaignLink->campaign) }}"
           class="text-sm text-gray-400 hover:text-gray-600">{{ $campaignLink->campaign?->name }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-sm font-semibold text-gray-900">Чат</span>
    </div>

    {{-- Контекст посилання --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4 text-xs text-gray-600 space-y-1">
        <div class="flex items-center gap-6 flex-wrap">
            <span>{{ __('client.chat_campaign') }} <strong class="text-gray-800">{{ $campaignLink->campaign?->name ?? '—' }}</strong></span>
            <span>{{ __('client.chat_site') }} <strong class="text-gray-800">{{ $campaignLink->site ? ($campaignLink->site->trashed() ? __('client.site_unavailable') : $campaignLink->site->domain) : '—' }}</strong></span>
            <span>Анкор: <strong class="text-gray-800">{{ $campaignLink->anchor ?: '—' }}</strong></span>
            @php
                $typeLabel = match($campaignLink->placement_type) {
                    'onclick'       => 'JS Onclick',
                    'link'          => 'SEO посилання',
                    'article_once', 'article_client'  => __('client.type_article_once'),
                    'article_daily','article_webmaster'=> __('client.type_article_daily'),
                    default         => $campaignLink->placement_type,
                };
            @endphp
            <span>Тип: <strong class="text-gray-800">{{ $typeLabel }}</strong></span>
            @if($campaignLink->donor_url)
            <a href="{{ $campaignLink->donor_url }}?t={{ time() }}" target="_blank"
               class="text-blue-500 hover:underline">Переглянути сторінку</a>
            @endif
        </div>
    </div>

    @include('_chat_window', [
        'senderType' => 'client',
        'pollUrl'    => route('client.chat.poll', $campaignLink),
        'sendUrl'    => route('client.chat.send', $campaignLink),
    ])
</div>
@endsection
