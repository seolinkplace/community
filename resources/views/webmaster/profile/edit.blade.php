@extends('webmaster.layouts.app')
@section('title', __('client.profile_title'))

@section('content')
<h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('client.profile_title') }}</h1>

<div class="max-w-2xl space-y-6">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('client.profile_basic') }}</h2>
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-400 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif
        @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-400 text-sm">
            {{ session('success') }}
        </div>
        @endif
        <form method="POST" action="{{ route('webmaster.profile.update') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $webmaster->name) }}"
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $webmaster->email) }}"
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                @if($webmaster instanceof \App\Models\UnifiedUser)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.email_language') }}</label>
                    <select name="locale" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="uk" {{ old('locale', $webmaster->locale) === 'uk' ? 'selected' : '' }}>{{ __('client.lang_uk') }}</option>
                        <option value="en" {{ old('locale', $webmaster->locale) === 'en' ? 'selected' : '' }}>{{ __('client.lang_en') }}</option>
                    </select>
                </div>
                @endif
            </div>
            <button type="submit" class="mt-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2 rounded-lg text-sm hover:bg-gray-700 dark:hover:bg-gray-100">{{ __('client.save') }}</button>
        </form>
    </div>

    {{-- Послуги --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-1">{{ __('client.services_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('client.services_desc') }}</p>
        <form method="POST" action="{{ route('webmaster.profile.update') }}">
            @csrf @method('PUT')
            @php $services = $webmaster->webmasterProfile?->services ?? []; @endphp
            <div class="space-y-3">
                @foreach([
                    'place_website'   => __('client.service_place_website'),
                    'place_social'    => __('client.service_place_social'),
                    'write'           => __('client.service_write'),
                    'write_and_place' => __('client.service_write_and_place'),
                ] as $val => $label)
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="services[]" value="{{ $val }}"
                           {{ in_array($val, $services) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-gray-900 accent-gray-900">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            <button type="submit" class="mt-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2 rounded-lg text-sm hover:bg-gray-700 dark:hover:bg-gray-100">{{ __('client.save') }}</button>
        </form>
    </div>

    {{-- Пароль --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('client.change_password') }}</h2>
        <form method="POST" action="{{ route('webmaster.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.current_password') }}</label>
                    <input type="password" name="current_password"
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.password') }}</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.password_confirm') }}</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2 rounded-lg text-sm hover:bg-gray-700 dark:hover:bg-gray-100">{{ __('client.change_password') }}</button>
        </form>
    </div>

    {{-- Google акаунт --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('auth.google_account') }}</h2>
        @if($webmaster instanceof \App\Models\UnifiedUser)
            @if($webmaster->google_id)
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('auth.google_linked_desc') }}</p>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">{{ $client->google_email ?? $webmaster->google_email ?? '' }}</p>
            <form method="POST" action="{{ route('auth.google.unlink') }}">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 rounded-lg text-sm hover:bg-red-50 dark:hover:bg-red-900/20">
                    {{ __('auth.google_unlink') }}
                </button>
            </form>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('auth.google_not_linked') }}</p>
            <a href="{{ route('auth.google.link') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                {{ __('auth.google_link') }}
            </a>
            @endif
        @endif
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
{{-- GDPR: експорт даних --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('auth.gdpr_export_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('auth.gdpr_export_desc') }}</p>
        <a href="{{ route('unified.export.data') }}"
           class="inline-block px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
            {{ __('auth.gdpr_export_btn') }}
        </a>
    </div>
    {{-- Видалення акаунта --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-red-200 dark:border-red-900 p-6">
        <h2 class="font-medium text-red-600 dark:text-red-400 mb-2">{{ __('auth.delete_account_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('auth.delete_account_desc') }}</p>
        <form method="POST" action="{{ route('unified.account.delete') }}"
              onsubmit="return confirm('{{ __('auth.delete_account_confirm') }}')">
            @csrf @method('DELETE')
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.delete_account_confirm') }}</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                    {{ __('auth.delete_account_btn') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
