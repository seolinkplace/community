@extends('performer.layouts.app')
@section('title', __('nav.profile'))
@section('content')
<div class="max-w-2xl mx-auto py-6 space-y-4">

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.personal_info') }}</h2>
        <form method="POST" action="{{ route('performer.profile.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.email') }}</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                {{ __('client.save') }}
            </button>
        </form>
    </div>

    {{-- Додати роль --}}
    @php $userRoles = auth('unified')->user()?->activeRoles() ?? []; @endphp
    @php $availableRoles = array_diff(['client','webmaster','performer'], $userRoles); @endphp
    @if(count($availableRoles) > 0)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-1">{{ __('auth.add_role_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('auth.add_role_hint') }}</p>
        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        <div class="flex flex-wrap gap-3">
            @foreach($availableRoles as $role)
            <form method="POST" action="{{ route('unified.add.role') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('nav.role_'.$role) }}
                    <span class="text-xs text-gray-400">({{ __('nav.role_'.$role.'_desc') }})</span>
                </button>
            </form>
            @endforeach
        </div>
    </div>
    @endif
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.change_password') }}</h2>
        <form method="POST" action="{{ route('performer.profile.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.current_password') }}</label>
                <input type="password" name="current_password" required
                       class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.new_password') }}</label>
                <input type="password" name="password" required
                       class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.password_confirm') }}</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                {{ __('client.save') }}
            </button>
        </form>
    </div>

</div>
@endsection
