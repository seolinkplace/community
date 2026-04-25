@php
    if (request()->is('wm/*')) {
        $layout = 'webmaster.layouts.app';
    } elseif (request()->is('performer/*')) {
        $layout = 'performer.layouts.app';
    } elseif (request()->is('app/*')) {
        $layout = 'client.layouts.app';
    } else {
        $userRoles = auth('unified')->user()?->roles->pluck('role')->toArray() ?? [];
        if (in_array('webmaster', $userRoles)) {
            $layout = 'webmaster.layouts.app';
        } elseif (in_array('performer', $userRoles)) {
            $layout = 'performer.layouts.app';
        } else {
            $layout = 'client.layouts.app';
        }
    }
@endphp
@extends($layout)
@section('title', $ticket->subject)
@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('unified.support.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex-1">{{ $ticket->subject }}</h1>
        @php
            $statusColors = [
                'open'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                'resolved'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                'closed'      => 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500',
            ];
        @endphp
        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? '' }}">
            {{ __('support.statuses.' . $ticket->status) }}
        </span>
    </div>

    {{-- Messages --}}
    <div class="space-y-4 mb-6">
        @foreach($messages as $msg)
        @php
            $isSupport = in_array($msg->sender_role, ['admin', 'moderator']);
            $isOwn     = !$isSupport && $msg->sender_id === auth('unified')->id();
        @endphp
        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[80%]">
                <div class="text-xs text-gray-400 dark:text-gray-500 mb-1 {{ $isOwn ? 'text-right' : 'text-left' }}">
                    @if($isSupport)
                        {{ __('support.support_label') }} · {{ $msg->created_at->diffForHumans() }}
                    @elseif($isOwn)
                        {{ __('support.you') }} · {{ $msg->created_at->diffForHumans() }}
                    @else
                        {{ $msg->sender->name ?? '—' }} · {{ $msg->created_at->diffForHumans() }}
                    @endif
                </div>
                <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap
                    {{ $isOwn
                        ? 'bg-blue-600 text-white rounded-br-sm'
                        : ($isSupport
                            ? 'bg-green-600 text-white rounded-bl-sm'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-sm') }}">
                    {{ $msg->message }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Reply form --}}
    @if(!$isClosed)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="POST" action="{{ route('unified.support.reply', $ticket->id) }}">
            @csrf
            <textarea name="message" rows="3" required maxlength="5000"
                      placeholder="{{ __('support.reply_placeholder') }}"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none mb-3"></textarea>
            <div class="flex items-center justify-between">
                {{-- Close ticket — окрема форма поза межами reply form --}}
                <button type="button"
                        onclick="document.getElementById('close-ticket-form').submit()"
                        class="text-xs text-gray-400 hover:text-red-500 transition">
                    {{ __('support.close_ticket') }}
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    {{ __('support.reply') }}
                </button>
            </div>
        </form>
        {{-- Close form окремо (не вкладена!) --}}
        <form id="close-ticket-form" method="POST" action="{{ route('unified.support.close', $ticket->id) }}" class="hidden">
            @csrf
        </form>
    </div>
    @else
    <div class="text-center py-6 text-gray-400 dark:text-gray-500 text-sm">
        {{ __('support.ticket_closed_notice') }}
    </div>
    @endif
</div>
@endsection
