@extends('client.layouts.app')
@section('title', __('client.articles_submit'))
@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.articles.catalog') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-sm">← {{ __('client.go_to_sites_catalog') }}</a>
        <span class="text-gray-300 dark:text-gray-600">/</span>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.articles_submit') }}</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('client.articles.store') }}"
          class="bg-white dark:bg-gray-900 rounded-xl shadow p-6 space-y-5">
        @csrf

        {{-- Site select --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.site_label') }}</label>
            <select name="site_uuid" id="site_uuid" required
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updatePrice(this)">
                <option value="">— {{ __('client.site_label') }} —</option>
                @foreach($sites as $site)
                <option value="{{ $site->uuid }}"
                        data-price="{{ $pricesByUuid[$site->uuid] ?? '' }}"
                        {{ (old('site_uuid', $preselectedSite?->uuid)) === $site->uuid ? 'selected' : '' }}>
                    {{ $site->domain }}@if($site->platform_type !== 'website') ({{ ucfirst($site->platform_type) }})@endif
                </option>
                @endforeach
            </select>
            <div id="site_price_hint" class="mt-1 text-xs text-blue-600 dark:text-blue-400 hidden">
                {{ __('client.col_price_article') }}: <strong id="site_price_value"></strong>
            </div>
        </div>

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.article_title_label') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Content --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('client.article_content') }}
                <span class="text-gray-400 font-normal">({{ __('client.article_content_hint') }})</span>
            </label>
            <textarea name="content" rows="10"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('content') }}</textarea>
        </div>

        {{-- Notes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.article_notes_label') }}</label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
        </div>

        @if(!auth('unified')->user()->rules_agreed_at)
        <div class="mb-3">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="rules_agreed" value="1" required
                       class="mt-0.5 flex-shrink-0 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {!! __('rules.agree_checkbox', ['url' => route('rules.index')]) !!}
                </span>
            </label>
            @error('rules_agreed')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        @else
        <div class="mb-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-3.5 h-3.5 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('rules.already_agreed', ['date' => auth('unified')->user()->rules_agreed_at->format('d.m.Y')]) }}
            </p>
        </div>
        @endif
        <div class="flex gap-3 pt-1">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                {{ __('client.articles_submit_btn') }}
            </button>
            <a href="{{ route('client.articles.index') }}"
               class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium px-5 py-2 rounded-lg text-sm">
                {{ __('client.cancel') }}
            </a>
        </div>
    </form>
</div>
<script>
function updatePrice(select) {
    const price = select.options[select.selectedIndex].dataset.price;
    const hint  = document.getElementById('site_price_hint');
    const val   = document.getElementById('site_price_value');
    if (price) {
        val.textContent = '$' + parseFloat(price).toFixed(2);
        hint.classList.remove('hidden');
    } else {
        hint.classList.add('hidden');
    }
}
// Init on page load if preselected
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('site_uuid');
    if (sel) updatePrice(sel);
});
</script>
@endsection
