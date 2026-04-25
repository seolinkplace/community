@extends('client.layouts.app')
@section('title', __('client.articles_sites_title'))
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.articles_sites_title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('client.articles_sites_subtitle') }}</p>
        </div>
        <a href="{{ route('client.articles.index') }}"
           class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ __('client.my_articles') }}
        </a>
    </div>

    {{-- Mode switcher --}}
    <div class="grid grid-cols-2 gap-3 mb-6">
        <a href="{{ route('client.catalog.index') }}"
           class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 hover:border-yellow-400 dark:hover:border-yellow-500 transition-colors">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ __('client.catalog_mode_links') }}</span>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 leading-snug">{{ __('client.catalog_desc_links') }}</p>
        </a>
        <div class="rounded-xl border-2 border-blue-400 bg-blue-50 dark:bg-blue-900/20 px-4 py-3">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ __('client.catalog_mode_articles') }}</span>
            </div>
            <p class="text-xs text-blue-600 dark:text-blue-500 leading-snug">{{ __('client.catalog_desc_articles') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('client.articles.catalog') }}" class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:items-end">

            <div class="col-span-2 sm:flex-1 sm:min-w-48">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="domain.com"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.platform_type') }}</label>
                <select name="platform"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('client.filter_platform_all') }}</option>
                    <option value="website" {{ request('platform') === 'website' ? 'selected' : '' }}>{{ __('client.filter_platform_website') }}</option>
                    @foreach(['facebook','instagram','tiktok','linkedin','telegram','youtube','twitter'] as $p)
                    <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>

            @if($niches->isNotEmpty())
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.niche') }}</label>
                <select name="niche"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('client.filter_all') }}</option>
                    @foreach($niches as $niche)
                    <option value="{{ $niche }}" {{ request('niche') === $niche ? 'selected' : '' }}>{{ $niche }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-span-2 grid grid-cols-2 gap-2 sm:flex sm:col-span-1">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    {{ __('client.filter_apply') }}
                </button>
                @if(request()->hasAny(['search','platform','niche']))
                <a href="{{ route('client.articles.catalog') }}"
                   class="text-center border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm px-4 py-2 rounded-lg">
                    {{ __('client.filter_reset') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($balance) && $balance <= 0)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-8 text-center">
        <p class="text-yellow-700 dark:text-yellow-400 text-sm mb-3">{{ __('client.err_no_balance_for_catalog') }}</p>
        <a href="{{ route('client.wallet') }}"
           class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-5 py-2 rounded-lg">
            {{ __('client.top_up_balance') }}
        </a>
    </div>
    @elseif($sites->isEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
        <p class="text-gray-400 text-sm">{{ __('client.articles_sites_empty') }}</p>
    </div>
    @else

    {{-- List --}}
    <div class="flex flex-col gap-2 mb-6">
        @foreach($sites as $site)
        @php
            $price    = $prices[$site->id]->price_article_once ?? null;
            $platformIcons = [
                'facebook'  => '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>',
                'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
                'tiktok'    => '<path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/>',
                'linkedin'  => '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
                'telegram'  => '<path d="M21 3L3 10.5l6.75 2.25L21 3zM21 3l-7.5 18-3.75-6.75L21 3z"/>',
                'youtube'   => '<path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>',
                'twitter'   => '<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>',
            ];
            $iconPath = $platformIcons[$site->platform_type] ?? null;
        @endphp
        <a href="{{ route('client.articles.create', ['site' => $site->uuid]) }}"
           class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-sm transition-all">
            {{-- Top row: icon + domain + price --}}
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-gray-800 mt-0.5">
                        @if($iconPath)
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">{!! $iconPath !!}</svg>
                        @else
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white break-all leading-tight">{{ $site->domain }}</div>
                        @if($site->niche)
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ $site->niche }}</span>
                        @endif
                    </div>
                </div>
                @if($price)
                <div class="flex-shrink-0 text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($price, 2) }}</div>
                    <div class="text-xs text-gray-400">{{ __('client.once') }}</div>
                </div>
                @endif
            </div>
            {{-- Metrics --}}
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mt-2 ml-11">
                @if($site->platform_type === 'website')
                    @if($site->dr)<span>DR <strong class="text-gray-700 dark:text-gray-300 font-medium">{{ $site->dr }}</strong></span>@endif
                    @if($site->traffic)<span>{{ __('client.col_traffic') }} <strong class="text-gray-700 dark:text-gray-300 font-medium">{{ number_format($site->traffic) }}</strong></span>@endif
                @else
                    @if($site->followers_count)<span>{{ __('client.col_followers') }} <strong class="text-gray-700 dark:text-gray-300 font-medium">{{ number_format($site->followers_count) }}</strong></span>@endif
                @endif
                @if($site->description)
                <span class="truncate hidden lg:block text-gray-400 dark:text-gray-500">{{ $site->description }}</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    @if($sites->hasPages())
    <div class="mt-2">{{ $sites->links() }}</div>
    @endif

    @endif
</div>
@endsection
