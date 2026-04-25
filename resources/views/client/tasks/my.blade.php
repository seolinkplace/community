@extends('client.layouts.app')
@section('title', __('client.tasks_my'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.tasks_my') }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('client.tasks.index') }}"
               class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                {{ __('client.tasks_available') }}
            </a>
            <a href="{{ route('client.tasks.create') }}"
               class="text-xs bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-3 py-1.5 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-100">
                + {{ __('client.tasks_create') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    @forelse($tasks as $task)
    @php
        $typeLabel = match($task->type) {
            'comment' => __('client.task_type_comment'),
            'vote'    => __('client.task_type_vote'),
            'like'    => __('client.task_type_like'),
            'share'   => __('client.task_type_share'),
            default   => $task->type
        };
        $sc = match($task->status) {
            'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'paused'    => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
            default     => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
        };
        $statusLabel = match($task->status) {
            'active'    => __('client.status_active'),
            'paused'    => __('client.status_paused'),
            'completed' => __('client.status_completed_n'),
            'cancelled' => __('client.status_cancelled_n'),
            default     => $task->status
        };
        $progress = $task->max_completions > 0 ? round($task->completions_count / $task->max_completions * 100) : 0;
    @endphp
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-3">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $sc }}">{{ $statusLabel }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $typeLabel }}</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $task->title }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 dark:text-gray-500 mt-2">
                    <span>{{ __('client.task_completions') }}: <strong class="text-gray-700 dark:text-gray-300">{{ $task->completions_count }}/{{ $task->max_completions }}</strong></span>
                    <span>{{ __('client.task_budget') }}: <strong class="text-gray-700 dark:text-gray-300">${{ number_format($task->budget_reserved, 2) }}</strong></span>
                </div>
                <div class="mt-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <div class="text-right flex-shrink-0 flex flex-col items-end gap-2">
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task->created_at->format('d.m.Y') }}</span>
                <a href="{{ route('client.tasks.show', $task) }}"
                   class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                    {{ __('client.view_details') }} →
                </a>
            </div>
        </div>

        {{-- Виконання на перевірці --}}
        @if($task->completions->where('status','pending')->count())
        <div class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.task_pending_review') }}:</p>
            @foreach($task->completions->where('status', 'pending') as $completion)
            <div class="flex flex-wrap items-center justify-between py-2 border-b border-gray-50 dark:border-gray-800 gap-2">
                <div class="text-xs text-gray-600 dark:text-gray-300 flex flex-wrap gap-2">
                    @if($completion->proof_url)
                        <a href="{{ $completion->proof_url }}" target="_blank" class="text-blue-500 dark:text-blue-400 hover:underline">{{ __('client.task_proof_link') }}</a>
                    @endif
                    @if($completion->proof_screenshot)
                        <a href="{{ Storage::url($completion->proof_screenshot) }}" target="_blank" class="text-blue-500 dark:text-blue-400 hover:underline">{{ __('client.task_screenshot_link') }}</a>
                    @endif
                    @if($completion->comment)
                        <span class="text-gray-400 dark:text-gray-500">{{ $completion->comment }}</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('client.tasks.review', $completion) }}">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">✓</button>
                    </form>
                    <form method="POST" action="{{ route('client.tasks.review', $completion) }}">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button class="text-xs border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-3 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">✗</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center text-gray-400 dark:text-gray-500 text-sm">
        {{ __('client.tasks_my_empty') }}
    </div>
    @endforelse

    @if($tasks->hasPages())
    <div class="mt-4">{{ $tasks->links() }}</div>
    @endif
</div>
@endsection
