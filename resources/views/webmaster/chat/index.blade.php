@extends('webmaster.layouts.app')
@section('title', __('nav.chats'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ __('nav.chats') }}</h1>
    @forelse($links as $link)
    <a href="{{ route('webmaster.chat.show', $link) }}"
       class="block bg-white dark:bg-gray-900 rounded-xl border {{ $link->unread_count ? 'border-blue-400 dark:border-blue-500' : 'border-gray-200 dark:border-gray-700' }} p-5 mb-3 hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1.5">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $link->anchor ?: __('client.no_anchor') }}</span>
                    @if($link->unread_count)
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-full px-2 py-0.5">
                        {{ $link->unread_count }} {{ __('client.new_messages') }}
                    </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">{{ $link->site?->domain ?? '—' }}</p>
                @if($link->last_message)
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ $link->last_message->sender_type === 'webmaster' ? __('client.you') : __('client.client') }}:</span>
                    {{ $link->last_message->body }}
                </p>
                @endif
            </div>
            <div class="flex-shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                {{ $link->last_message?->created_at->format('d.m H:i') }}
            </div>
        </div>
    </a>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center text-gray-400 dark:text-gray-500 text-sm">
        {{ __('client.no_chats') }}
    </div>
    @endforelse
</div>
@endsection
