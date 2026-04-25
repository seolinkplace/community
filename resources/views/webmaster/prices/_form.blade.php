@php $isEdit = !is_null($price); @endphp

{{-- Сайт (тільки при створенні) --}}
@if(!$isEdit)
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.site_label') }}</label>
    <select name="site_id" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @foreach($sites as $site)
            <option value="{{ $site->id }}" {{ (old('site_id', $selectedSiteId) == $site->id) ? 'selected' : '' }}>
                {{ $site->domain }}
            </option>
        @endforeach
    </select>
</div>

{{-- Тип scope --}}
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.prices_scope_type') }}</label>
    <select name="scope_type" id="scope_type" required onchange="updateScopeFields()"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="site_default" {{ old('scope_type') === 'site_default' ? 'selected' : '' }}>{{ __('client.scope_site_default') }}</option>
        <option value="depth"        {{ old('scope_type') === 'depth'        ? 'selected' : '' }}>{{ __('client.scope_depth') }}</option>
        <option value="url"          {{ old('scope_type') === 'url'          ? 'selected' : '' }}>{{ __('client.scope_url') }}</option>
        <option value="url_client"   {{ old('scope_type') === 'url_client'   ? 'selected' : '' }}>{{ __('client.scope_url_client') }}</option>
    </select>
    <p class="text-xs text-gray-400 mt-1">{{ __('client.scope_priority_hint') }}</p>
</div>

{{-- Depth --}}
<div id="field_depth" class="hidden">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.scope_depth') }}</label>
    <select name="scope_depth"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="0" {{ old('scope_depth') == 0 ? 'selected' : '' }}>{{ __('client.depth_0') }}</option>
        <option value="1" {{ old('scope_depth') == 1 ? 'selected' : '' }}>{{ __('client.depth_1') }}</option>
        <option value="2" {{ old('scope_depth') == 2 ? 'selected' : '' }}>{{ __('client.depth_2') }}</option>
        <option value="3" {{ old('scope_depth') == 3 ? 'selected' : '' }}>{{ __('client.depth_3') }}</option>
        <option value="4" {{ old('scope_depth') == 4 ? 'selected' : '' }}>{{ __('client.depth_4') }}</option>
    </select>
</div>

{{-- URL --}}
<div id="field_url" class="hidden">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.scope_url_label') }}</label>
    <input type="url" name="scope_url" value="{{ old('scope_url') }}"
           placeholder="https://example.com/page/"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>

{{-- Client --}}
<div id="field_client" class="hidden">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.client_label') }}</label>
    <select name="client_id"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">{{ __('client.select_client') }}</option>
        @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                {{ $client->email }}
            </option>
        @endforeach
    </select>
</div>
@endif

{{-- Ціни --}}
<div class="border-t border-gray-100 pt-4">
    <p class="text-sm font-medium text-gray-700 mb-3">{{ __('client.prices_usd_label') }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ __('client.price_link_day') }}</label>
            <input type="number" name="price_link_per_day" step="0.01" min="0"
                   value="{{ old('price_link_per_day', $price?->price_link_per_day) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="0.00">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ __('client.price_onclick_day') }}</label>
            <input type="number" name="price_onclick_per_day" step="0.01" min="0"
                   value="{{ old('price_onclick_per_day', $price?->price_onclick_per_day) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="0.00">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ __('client.article_client_price') }}</label>
            <input type="number" name="price_article_once" step="0.01" min="0"
                   value="{{ old('price_article_once', $price?->price_article_once) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="0.00">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ __('client.article_wm_price') }}</label>
            <input type="number" name="price_article_per_day" step="0.01" min="0"
                   value="{{ old('price_article_per_day', $price?->price_article_per_day) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="0.00">
        </div>
    </div>
</div>

{{-- Базова ціна + коефіцієнти (опційно) --}}
<details class="border border-gray-200 rounded-lg">
    <summary class="px-4 py-3 text-sm text-gray-600 cursor-pointer hover:bg-gray-50">
        {{ __('client.prices_base_coef') }}
    </summary>
    <div class="px-4 pb-4 pt-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs text-gray-500 mb-1">{{ __('client.prices_base_day') }}</label>
            <input type="number" name="base_price_per_day" step="0.01" min="0"
                   value="{{ old('base_price_per_day', $price?->base_price_per_day) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                   placeholder="0.00">
        </div>
        @foreach(['coef_link' => __('client.coef_link'), 'coef_onclick' => __('client.coef_onclick'), 'coef_article_once' => __('client.coef_article_once'), 'coef_article_daily' => __('client.coef_article_daily')] as $field => $label)
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $label }}</label>
            <input type="number" name="{{ $field }}" step="0.01" min="0"
                   value="{{ old($field, $price?->$field) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                   placeholder="1.00">
        </div>
        @endforeach
    </div>
</details>

{{-- Налаштування --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-xs text-gray-500 mb-1">{{ __('client.prices_max_placements') }}</label>
        <input type="number" name="max_placements" min="1"
               value="{{ old('max_placements', $price?->max_placements) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="∞">
    </div>
    <div class="flex items-center gap-2 pt-5">
        <input type="checkbox" name="is_public" id="is_public" value="1"
               {{ old('is_public', $price?->is_public) ? 'checked' : '' }}
               class="rounded border-gray-300 text-blue-600">
        <label for="is_public" class="text-sm text-gray-700">{{ __('client.prices_public') }}</label>
    </div>
    <div class="flex items-center gap-2 pt-5">
        <input type="checkbox" name="adult_allowed" id="adult_allowed" value="1"
               {{ old('adult_allowed', $price?->adult_allowed) ? 'checked' : '' }}
               class="rounded border-gray-300 text-red-600">
        <label for="adult_allowed" class="text-sm text-gray-700">{{ __('client.adult_label') }}</label>
    </div>
</div>

@if(!$isEdit)
<script>
function updateScopeFields() {
    const type = document.getElementById('scope_type').value;
    document.getElementById('field_depth').classList.toggle('hidden', type !== 'depth');
    document.getElementById('field_url').classList.toggle('hidden', !['url','url_client'].includes(type));
    document.getElementById('field_client').classList.toggle('hidden', type !== 'url_client');
}
document.addEventListener('DOMContentLoaded', updateScopeFields);
</script>
@endif
