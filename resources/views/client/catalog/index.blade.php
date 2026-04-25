@extends('client.layouts.app')
@section('title', __('client.catalog_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    {{-- Mode switcher --}}
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-4 py-3">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                <span class="text-sm font-bold text-yellow-700 dark:text-yellow-400">{{ __('client.catalog_mode_links') }}</span>
            </div>
            <p class="text-xs text-yellow-600 dark:text-yellow-500 leading-snug">{{ __('client.catalog_desc_links') }}</p>
        </div>
        <a href="{{ route('client.articles.catalog') }}"
           class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ __('client.catalog_mode_articles') }}</span>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 leading-snug">{{ __('client.catalog_desc_articles') }}</p>
        </a>
    </div>

    @if(isset($balance) && $balance <= 0)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 text-center">
        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-400 mb-3">{{ __('client.catalog_empty_balance') }}</p>
        <a href="{{ route('client.wallet') }}"
           class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
            {{ __('client.top_up_balance') }}
        </a>
    </div>
    @else

    {{-- Фільтри --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 mb-4 p-4">
        <form method="GET" class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('client.search_domain') }}"
                class="col-span-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white">
            <select name="niche" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">{{ __('client.all_niches') }}</option>
                @foreach($niches as $niche)
                    <option value="{{ $niche }}" {{ request('niche') === $niche ? 'selected' : '' }}>{{ $niche }}</option>
                @endforeach
            </select>
            <select name="platform" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">{{ __('client.all_platforms') }}</option>
                @foreach($platforms ?? [] as $platform)
                    <option value="{{ $platform }}" {{ request('platform') === $platform ? 'selected' : '' }}>
                        {{ $platform === 'website' ? __('client.platform_website') : ucfirst($platform) }}
                    </option>
                @endforeach
            </select>
            <select name="service" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">{{ __('client.all_services') }}</option>
                <option value="articles" {{ request('service') === 'articles' ? 'selected' : '' }}>{{ __('client.service_articles') }}</option>
                <option value="writing" {{ request('service') === 'writing' ? 'selected' : '' }}>{{ __('client.service_writing') }}</option>
                <option value="social" {{ request('service') === 'social' ? 'selected' : '' }}>{{ __('client.service_social') }}</option>
            </select>
            <input type="number" name="min_dr" value="{{ request('min_dr') }}" placeholder="{{ __('client.dr_from') }}"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('client.price_to') }}"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <div class="col-span-2 grid grid-cols-2 gap-2">
                <button type="submit" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100">{{ __('client.filter_btn') }}</button>
                <a href="{{ route('client.catalog.index') }}" class="text-center px-4 py-2 rounded-lg text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('client.reset') }}</a>
            </div>
        </form>
    </div>

    @if($sites->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-400 text-sm">
            {{ __('client.no_sites') }}
        </div>
    @else

    {{-- Картки --}}
    <div class="flex flex-col gap-2">
        @foreach($sites as $site)
        @php
            $services = $site->unifiedUser?->webmasterProfile?->services ?? [];
            $hasArticles = $site->content_type === 'article' || $site->content_type === 'both';
        @endphp
        <a href="{{ route('client.catalog.show', $site) }}"
           class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:border-yellow-400 dark:hover:border-yellow-500 transition">
            {{-- Header row: домен + ціна --}}
            <div class="px-3 py-2.5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-gray-400 flex-shrink-0">
                            <x-platform-icon :platform="$site->platform_type ?? 'website'" />
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white text-sm break-all leading-tight">
                            {{ $site->platform_type !== 'website' && $site->platform_url ? parse_url($site->platform_url, PHP_URL_HOST) : $site->domain }}
                        </span>
                    </div>
                    <span class="flex-shrink-0 font-bold text-gray-900 dark:text-white text-sm text-right">
                        @if(request('service') === 'articles' && isset($articlePrices[$site->id]))
                            ${{ number_format($articlePrices[$site->id]->price_article_once, 2) }}
                            <span class="text-xs font-normal text-gray-400 block">{{ __('client.once') }}</span>
                        @else
                            {{ $site->price ? '$'.$site->price : '—' }}
                            <span class="text-xs font-normal text-gray-400 block">{{ __('client.price_placement') }}</span>
                        @endif
                    </span>
                </div>
                @if($hasArticles || in_array('write', $services) || in_array('write_and_place', $services))
                <div class="flex flex-wrap gap-1 mt-1.5 ml-5">
                    @if($hasArticles)
                    <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ __('client.badge_articles') }}</span>
                    @endif
                    @if(in_array('write', $services) || in_array('write_and_place', $services))
                    <span class="text-xs px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">{{ __('client.badge_writing') }}</span>
                    @endif
                </div>
                @endif
            </div>
            {{-- Metrics row --}}
            <div class="grid grid-cols-4 divide-x divide-gray-100 dark:divide-gray-800 text-xs">
                <div class="px-2 py-2 text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $site->dr ?? '—' }}</div>
                    <div class="text-gray-400">DR</div>
                </div>
                <div class="px-2 py-2 text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $site->traffic ? number_format($site->traffic) : '—' }}</div>
                    <div class="text-gray-400">{{ __('client.traffic') }}</div>
                </div>
                <div class="px-2 py-2 text-center">
                    <div class="font-bold {{ $site->spam_score !== null && $site->spam_score > 30 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">{{ $site->spam_score ?? '—' }}</div>
                    <div class="text-gray-400">Spam</div>
                </div>
                <div class="px-2 py-2 text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $site->pages_count ? number_format($site->pages_count) : '—' }}</div>
                    <div class="text-gray-400">{{ __('client.pages_in_system') }}</div>
                </div>
            </div>
            @if($site->niche || $site->language)
            <div class="px-3 py-1.5 text-xs text-gray-400 flex items-center gap-2 border-t border-gray-100 dark:border-gray-800">
                @if($site->niche)<span class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $site->niche }}</span>@endif
                @if($site->language)<span>{{ $site->language }}</span>@endif
            </div>
            @endif
        </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $sites->links() }}</div>

    @endif
    @endif
</div>
@endsection
