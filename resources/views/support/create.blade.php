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
@section('title', __('support.new_ticket'))
@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('unified.support.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('support.new_ticket') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('unified.support.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('support.subject') }}</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                @error('subject')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('support.priority') }}</label>
                <select name="priority" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(['low','normal','high'] as $p)
                        <option value="{{ $p }}" {{ old('priority','normal') === $p ? 'selected' : '' }}>
                            {{ __('support.priorities.' . $p) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('support.message') }}</label>
                <textarea name="message" rows="6" required maxlength="5000"
                          placeholder="{{ __('support.reply_placeholder') }}"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition">
                    {{ __('support.send') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
