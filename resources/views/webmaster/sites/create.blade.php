@extends('webmaster.layouts.app')
@section('title', __('client.add_site'))

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Додати сайт</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('webmaster.sites.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-5">
                @include('webmaster.sites.partials.platform-select')
                {{-- Для соцмереж --}}
                <div id="field_platform_url" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.platform_url') }} *</label>
                    <input type="url" name="platform_url" value="{{ old('platform_url') }}" placeholder="https://t.me/mychannel"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div id="field_followers" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.followers_count') }}</label>
                    <input type="number" name="followers_count" value="{{ old('followers_count', 0) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div id="field_domain">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_domain') }} *</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" placeholder="example.com"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_niche') }}</label>
                        <input type="text" name="niche" value="{{ old('niche') }}" placeholder="IT, Finance..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.site_languages') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($languages as $code => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="languages[]" value="{{ $code }}"
                                   {{ in_array($code, old('languages', $siteLangs)) ? 'checked' : '' }}
                                   class="rounded border-gray-300">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_dr') }}</label>
                        <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">—</div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('client.field_set_by_admin') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_traffic') }}</label>
                        <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">—</div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('client.field_set_by_admin') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_content_type') }} *</label>
                        <select name="content_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="both" {{ old('content_type') === 'both' ? 'selected' : '' }}>{{ __('client.content_type_both') }}</option>
                            <option value="article" {{ old('content_type') === 'article' ? 'selected' : '' }}>{{ __('client.content_type_article') }}</option>
                            <option value="link_insert" {{ old('content_type') === 'link_insert' ? 'selected' : '' }}>{{ __('client.content_type_link') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_price') }}</label>
                        <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_description') }}</label>
                    <textarea name="description" rows="3" placeholder="Коротко про сайт..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_contact') }}</label>
                    <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Telegram, email..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_visibility') }} *</label>
                    <select name="visibility" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Публічний (в маркетплейсі)</option>
                        <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>{{ __('client.visibility_private_opt') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-700">Додати сайт</button>
                <a href="{{ route('webmaster.sites.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('client.cancel') }}</a>
            </div>
        
<script>
function updatePlatformFields() {
    var el = document.getElementById('platform_type'); var t = el ? el.value : 'website';
    var isSocial = t !== 'website';
    document.getElementById('field_platform_url').classList.toggle('hidden', !isSocial);
    document.getElementById('field_followers').classList.toggle('hidden', !isSocial);
    document.getElementById('field_domain').classList.toggle('hidden', isSocial);
}
document.addEventListener('DOMContentLoaded', updatePlatformFields);
</script>
</form>
    </div>
</div>
@endsection
