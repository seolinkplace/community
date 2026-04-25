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
@section('title', __('support.title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('support.my_tickets') }}</h1>
        <a href="{{ route('unified.support.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('support.new_ticket') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="text-center py-16 text-gray-500 dark:text-gray-400">
            <div class="flex justify-center mb-3">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="opacity-40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p class="text-sm">{{ __('support.no_tickets') }}</p>
        </div>
    @else

    {{-- Mobile: картки --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($tickets as $ticket)
        @php
            $statusColors = [
                'open'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                'resolved'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                'closed'      => 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500',
            ];
        @endphp
        <a href="{{ route('unified.support.show', $ticket->id) }}"
           class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-gray-400 dark:hover:border-gray-500 transition">
            <div class="flex items-start justify-between gap-2 mb-2">
                <span class="font-medium text-gray-900 dark:text-white">{{ $ticket->subject }}</span>
                <span class="flex-shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? '' }}">
                    {{ __('support.statuses.' . $ticket->status) }}
                </span>
            </div>
            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <span>{{ __('support.priorities.' . $ticket->priority) }}</span>
                <div class="flex items-center gap-2">
                    @if($ticket->unread_count > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400">
                            {{ $ticket->unread_count }} {{ __('support.unread') }}
                        </span>
                    @endif
                    <span>{{ $ticket->last_reply_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Desktop: таблиця --}}
    <div class="hidden md:block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">{{ __('support.subject') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('support.status') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('support.priority') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('support.last_reply') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($tickets as $ticket)
                @php
                    $statusColors = [
                        'open'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                        'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                        'resolved'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        'closed'      => 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500',
                    ];
                    $priorityColors = [
                        'low'    => 'text-gray-400',
                        'normal' => 'text-gray-600 dark:text-gray-300',
                        'high'   => 'text-red-500',
                    ];
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition cursor-pointer"
                    onclick="window.location='{{ route('unified.support.show', $ticket->id) }}'">
                    <td class="px-4 py-3 text-gray-400 dark:text-gray-500">{{ $ticket->id }}</td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $ticket->subject }}</span>
                        @if($ticket->unread_count > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400">
                                {{ $ticket->unread_count }} {{ __('support.unread') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? '' }}">
                            {{ __('support.statuses.' . $ticket->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs {{ $priorityColors[$ticket->priority] ?? '' }}">
                            {{ __('support.priorities.' . $ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">
                        {{ $ticket->last_reply_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $tickets->links() }}</div>
    </div>
    <div class="mt-4 md:hidden">{{ $tickets->links() }}</div>

    @endif
</div>
@endsection
