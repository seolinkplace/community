@extends('performer.layouts.app')
@section('title', __('nav.my_completions'))
@section('content')
<div class="max-w-3xl mx-auto py-6 px-4">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('nav.my_completions') }}</h2>
        <a href="{{ route('performer.tasks.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; {{ __('nav.available_tasks') }}</a>
    </div>

    @if($completions->isEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <p class="text-gray-500 dark:text-gray-400">{{ __('client.no_completions') }}</p>
        <a href="{{ route('performer.tasks.index') }}" class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('nav.available_tasks') }} &rarr;</a>
    </div>
    @else
    <div class="space-y-3">
        @foreach($completions as $completion)
        @php
            $sc = match($completion->status) {
                'pending'  => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                'claimed'  => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                default    => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
            };
            $sl = match($completion->status) {
                'pending'  => __('client.status_pending'),
                'approved' => __('client.status_approved'),
                'rejected' => __('client.status_rejected'),
                'claimed'  => __('client.status_claimed'),
                default    => $completion->status,
            };
            $isUrl = filter_var($completion->proof_url, FILTER_VALIDATE_URL);
        @endphp
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start justify-between gap-4 mb-3">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $completion->task?->title ?? '—' }}</h3>
                <div class="flex flex-col items-end gap-1 flex-shrink-0">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($completion->task?->reward ?? 0, 2) }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">{{ $sl }}</span>
                </div>
            </div>

            {{-- Proof --}}
            @if($completion->proof_url)
                @if($isUrl)
                    <a href="{{ $completion->proof_url }}" target="_blank"
                       class="text-xs text-indigo-500 dark:text-indigo-400 hover:underline break-all block mb-2">
                        {{ $completion->proof_url }}
                    </a>
                @else
                    <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 mb-2">
                        {{ $completion->proof_url }}
                    </div>
                @endif
            @endif

            {{-- Причина відхилення --}}
            @if($completion->status === 'rejected' && $completion->comment)
                <div class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-3 py-2 mb-2">
                    {{ $completion->comment }}
                </div>
            @endif

            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $completion->created_at->format('d.m.Y H:i') }}</p>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $completions->links() }}</div>
    @endif

</div>
@endsection
