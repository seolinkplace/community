@extends('client.layouts.app')
@section('title', 'Чати')
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-xl font-bold text-gray-900 mb-6">Чати</h1>

    @forelse($links as $link)
    <a href="{{ route('client.chat.show', $link) }}"
       class="block bg-white rounded-xl border {{ $link->unread_count ? 'border-blue-400' : 'border-gray-200' }} p-5 mb-3 hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1.5">
                    <span class="text-sm font-semibold text-gray-900">{{ $link->anchor ?: '(без анкора)' }}</span>
                    @if($link->unread_count)
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5">
                        {{ $link->unread_count }} нових
                    </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mb-1.5">{{ $link->site ? ($link->site->trashed() ? __('client.site_unavailable') : $link->site->domain) : '—' }} · {{ $link->campaign?->name ?? '—' }}</p>
                @if($link->last_message)
                <p class="text-xs text-gray-500 truncate">
                    <span class="font-medium text-gray-600">{{ $link->last_message->sender_type === 'client' ? 'Ви' : 'Вебмастер' }}:</span>
                    {{ $link->last_message->body }}
                </p>
                @endif
            </div>
            <div class="flex-shrink-0 text-xs text-gray-400 whitespace-nowrap">
                {{ $link->last_message?->created_at->format('d.m H:i') }}
            </div>
        </div>
    </a>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400 text-sm">
        Чатів поки немає.
    </div>
    @endforelse
</div>
@endsection
