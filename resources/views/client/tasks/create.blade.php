@extends('client.layouts.app')
@section('title', __('client.tasks_create'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.tasks.my') }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.tasks_create') }}</h2>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('client.tasks.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_title') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_description') }}
                </label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_url') }} <span class="text-red-500">*</span>
                </label>
                <input type="url" name="url" value="{{ old('url') }}" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="https://example.com/page">
                @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Task type — з БД, з описом --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_action_type') }} <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" x-data="{ selected: '{{ old('task_type_id', $taskTypes->first()?->id) }}' }">
                    @foreach($taskTypes as $type)
                    <label class="relative flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                           :class="selected == '{{ $type->id }}'
                               ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/30'
                               : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                        <input type="radio" name="task_type_id" value="{{ $type->id }}"
                               x-model="selected"
                               class="mt-0.5 text-indigo-600 focus:ring-indigo-500 shrink-0">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white leading-tight">
                                {{ $type->name() }}
                            </p>
                            @if($type->description())
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $type->description() }}
                            </p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('task_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Deadline --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_deadline_label') }}
                </label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('expires_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Reward + Max completions --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_reward') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="reward" value="{{ old('reward', '0.10') }}"
                           min="0.001" step="0.001" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('reward')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_max_completions') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_completions" value="{{ old('max_completions', 10) }}"
                           min="1" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('max_completions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Per-user limits --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_per_user_limit') }}
                    </label>
                    <input type="number" name="per_user_limit" value="{{ old('per_user_limit', 1) }}" min="0"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">{{ __('client.task_per_user_limit_hint') }}</p>
                    @error('per_user_limit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_per_user_daily_limit') }}
                    </label>
                    <input type="number" name="per_user_daily_limit" value="{{ old('per_user_daily_limit', 0) }}" min="0"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">{{ __('client.task_per_user_limit_hint') }}</p>
                    @error('per_user_daily_limit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Verification type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_verification_type') }} <span class="text-red-500">*</span>
                </label>
                <select name="verification_type" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\Task::verificationTypes() as $vtype)
                        <option value="{{ $vtype }}" {{ old('verification_type', 'screenshot') === $vtype ? 'selected' : '' }}>
                            {{ __('client.verification_' . $vtype) }}
                        </option>
                    @endforeach
                </select>
                @error('verification_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Verification instructions --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_verification_instructions') }}
                </label>
                <textarea name="verification_instructions" rows="3"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="{{ __('client.task_verification_instructions_placeholder') }}">{{ old('verification_instructions') }}</textarea>
                @error('verification_instructions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>



            {{-- Claim duration --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_claim_duration') }}
                </label>
                <select name="claim_duration_minutes"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="" {{ old('claim_duration_minutes') === null ? 'selected' : '' }}>
                        {{ __('client.task_claim_duration_none') }}
                    </option>
                    @foreach([10 => 'task_claim_duration_10m', 60 => 'task_claim_duration_1h', 180 => 'task_claim_duration_3h', 360 => 'task_claim_duration_6h', 720 => 'task_claim_duration_12h', 1440 => 'task_claim_duration_24h', 2880 => 'task_claim_duration_48h'] as $minutes => $key)
                    <option value="{{ $minutes }}" {{ (string)old('claim_duration_minutes') === (string)$minutes ? 'selected' : '' }}>
                        {{ __('client.' . $key) }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Auto-approve --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.task_auto_approve') }}
                </label>
                <select name="auto_approve_hours"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="" {{ old('auto_approve_hours') === null ? 'selected' : '' }}>
                        {{ __('client.task_auto_approve_off') }}
                    </option>
                    @foreach([6, 12, 24, 48] as $hours)
                    <option value="{{ $hours }}" {{ (string)old('auto_approve_hours') === (string)$hours ? 'selected' : '' }}>
                        {{ __('client.task_auto_approve_' . $hours . 'h') }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">{{ __('client.task_auto_approve_hint') }}</p>
                @error('auto_approve_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Budget hint --}}
            <p class="text-xs text-gray-400">{{ __('client.task_budget_hint') }}</p>

            <button type="submit"
                class="w-full px-4 py-2.5 bg-white dark:bg-white text-gray-900 font-semibold rounded-lg
                       border border-gray-300 hover:bg-gray-50 transition-colors text-sm">
                {{ __('client.tasks_create') }}
            </button>
        </form>
    </div>
</div>
@endsection
