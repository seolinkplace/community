{{-- Shared task list partial --}}
{{-- Required vars: $tasks, $taskTypes, $completeRoute, $myRoute, $createRoute, $indexRoute --}}

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.tasks_available') }}</h1>
    <div class="flex gap-2">
        <a href="{{ route($myRoute) }}"
           class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
            {{ __('client.tasks_my') }}
        </a>
        <a href="{{ route($createRoute) }}"
           class="text-xs bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-3 py-1.5 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-100">
            + {{ __('client.tasks_create') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-xl px-4 py-3 mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-xl px-4 py-3 mb-4">{{ session('error') }}</div>
@endif

{{-- Фільтр --}}
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-40">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.task_filter_type') }}</label>
            <select name="task_type_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">{{ __('client.task_filter_all') }}</option>
                @foreach($taskTypes as $type)
                    <option value="{{ $type->id }}" {{ request('task_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium">{{ __('client.filter_btn') }}</button>
            <a href="{{ route($indexRoute) }}" class="px-4 py-2 rounded-lg text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('client.reset') }}</a>
        </div>
    </form>
</div>

@if($tasks->isEmpty())
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('client.tasks_empty') }}</p>
</div>
@else
<div class="space-y-3">
    @foreach($tasks as $task)
    @php
        $pct = $task->max_completions > 0 ? min(100, round($task->completions_count / $task->max_completions * 100)) : 0;
        $verColors = [
            'screenshot'  => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
            'url'         => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'text_report' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
            'none'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
        ];
        $verColor = $verColors[$task->verification_type] ?? $verColors['screenshot'];
    @endphp
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="p-5">
            {{-- Теги + ціна --}}
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex flex-wrap gap-1.5">
                    @if($task->taskType)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">{{ $task->taskType->name() }}</span>
                    @endif
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $verColor }}">{{ __('client.verification_' . $task->verification_type) }}</span>
                    @if($task->claim_duration_minutes)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $task->claim_duration_minutes }}{{ __('client.minutes') }}
                        </span>
                    @endif
                    @if($task->per_user_limit === 1)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('client.task_once_per_user') }}</span>
                    @endif
                </div>
                <span class="text-xl font-bold text-green-600 dark:text-green-400 flex-shrink-0">${{ number_format($task->reward, 2) }}</span>
            </div>

            {{-- Назва і опис --}}
            <h3 class="font-semibold text-gray-900 dark:text-white text-base mb-1">{{ $task->title }}</h3>
            @if($task->description)
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $task->description }}</p>
            @endif

            {{-- URL --}}
            @if($task->url)
                <a href="{{ $task->url }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:underline mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    {{ $task->url }}
                </a>
            @endif

            {{-- Інструкція --}}
            @if($task->verification_instructions)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-4 py-3 mb-3 text-sm text-amber-800 dark:text-amber-300">
                    {{ $task->verification_instructions }}
                </div>
            @endif

            {{-- Мета-інфо --}}
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mb-4">
                <span>{{ __('client.task_completions') }}: {{ $task->completions_count }}/{{ $task->max_completions }}</span>
                @if($task->expires_at)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('client.task_deadline') }}: {{ $task->expires_at->format('d.m.Y') }}
                    </span>
                @endif
            </div>

            {{-- Прогрес-бар --}}
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mb-4">
                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
            </div>

            {{-- Кнопка --}}
            <button type="button"
                onclick="toggleTaskForm('{{ $task->uuid }}')"
                id="tbtn-{{ $task->uuid }}"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                {{ __('client.task_complete_btn') }}
            </button>
        </div>

        {{-- Форма (прихована) --}}
        <div id="tform-{{ $task->uuid }}" class="hidden border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 px-5 py-4">

            @if($task->claim_duration_minutes)
            <div class="flex items-start gap-2 mb-3 text-xs text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ __('client.time_limit_hint', ['minutes' => $task->claim_duration_minutes]) }}</span>
            </div>
            @endif

            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                @if($task->verification_type === 'text_report')
                    {{ __('client.proof_text_hint') }}
                @elseif($task->verification_type === 'screenshot')
                    {{ __('client.proof_screenshot_hint') }}
                @elseif($task->verification_type === 'none')
                    {{ __('client.proof_none_hint') }}
                @else
                    {{ __('client.proof_url_hint') }}
                @endif
            </p>

            <form method="POST" action="{{ route($completeRoute, $task) }}" enctype="multipart/form-data">
                @csrf
                @if($task->verification_type === 'text_report')
                    <textarea name="comment" rows="3" required
                        placeholder="{{ __('client.proof_text_placeholder') }}"
                        class="w-full text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 mb-3 text-gray-900 dark:text-white outline-none focus:border-indigo-500 resize-none"></textarea>
                @elseif($task->verification_type === 'screenshot')
                    <input type="file" name="proof_screenshot" accept="image/*" required
                        class="w-full text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 mb-3 text-gray-900 dark:text-white outline-none">
                @elseif($task->verification_type === 'url')
                    <input type="url" name="proof_url" required
                        placeholder="{{ __('client.proof_url_placeholder') }}"
                        class="w-full text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 mb-3 text-gray-900 dark:text-white outline-none focus:border-indigo-500">
                @endif
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        {{ __('client.task_submit_proof') }}
                    </button>
                    <button type="button"
                        onclick="toggleTaskForm('{{ $task->uuid }}')"
                        class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        {{ __('client.cancel') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
    @endforeach
</div>
@if($tasks->hasPages())
    <div class="mt-4">{{ $tasks->links() }}</div>
@endif
@endif
