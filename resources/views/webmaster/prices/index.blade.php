@extends('webmaster.layouts.app')
@section('title', __('client.prices_title'))
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('client.prices_title') }}</h1>
        <a href="{{ route('webmaster.prices.create', ['site' => $selectedSiteId]) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + {{ __('client.add_rule') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Фільтри --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="site_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach($sites as $site)
                <option value="{{ $site->id }}" {{ $selectedSiteId == $site->id ? 'selected' : '' }}>
                    {{ $site->domain }}
                </option>
            @endforeach
        </select>
        <select name="scope_type" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Всі типи</option>
            <option value="site_default" {{ request('scope_type') === 'site_default' ? 'selected' : '' }}>{{ __('client.scope_site_default') }}</option>
            <option value="depth"        {{ request('scope_type') === 'depth'        ? 'selected' : '' }}>{{ __('client.scope_depth') }}</option>
            <option value="url"          {{ request('scope_type') === 'url'          ? 'selected' : '' }}>Конкретна сторінка</option>
            <option value="url_client"   {{ request('scope_type') === 'url_client'   ? 'selected' : '' }}>{{ __('client.scope_url_client') }}</option>
        </select>
    </form>

    {{-- Легенда пріоритетів --}}
    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-xs text-blue-800">
        <strong>Пріоритет цін:</strong>
        {{ __('client.priority_full') }}
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        @if($prices->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                {{ __('client.no_rules_add') }} <a href="{{ route('webmaster.prices.create', ['site' => $selectedSiteId]) }}" class="text-blue-600 hover:underline">{{ __('client.add_first') }}</a>
            </div>
        @else
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">{{ __('client.prices_scope_type') }}</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">Посилання/день</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">{{ __('client.price_onclick_day') }}</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">{{ __('client.col_article_once') }}</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">{{ __('client.col_article_daily') }}</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Макс.</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Публ.</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($prices as $price)
                @php
                    $scopeColors = [
                        'site_default' => 'bg-gray-100 text-gray-700',
                        'depth'        => 'bg-purple-100 text-purple-700',
                        'url'          => 'bg-blue-100 text-blue-700',
                        'url_client'   => 'bg-green-100 text-green-700',
                    ];
                    $color = $scopeColors[$price->scope_type] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $color }} mb-1">
                            {{ __('client.' . \App\Models\PagePrice::SCOPE_LABELS[$price->scope_type]) }}
                        </span>
                        <div class="text-gray-500 text-xs truncate max-w-xs">
                            {{ $price->getScopeLabel() }}
                            @if($price->client)
                                <span class="text-green-600">({{ $price->client->email }})</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($price->price_link_per_day)
                            <span class="font-medium">${{ number_format($price->price_link_per_day, 2) }}</span>
                        @elseif($price->base_price_per_day && $price->coef_link)
                            <span class="text-gray-400">${{ number_format($price->base_price_per_day * $price->coef_link, 2) }}*</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($price->price_onclick_per_day)
                            <span class="font-medium">${{ number_format($price->price_onclick_per_day, 2) }}</span>
                        @elseif($price->base_price_per_day && $price->coef_onclick)
                            <span class="text-gray-400">${{ number_format($price->base_price_per_day * $price->coef_onclick, 2) }}*</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($price->price_article_once)
                            <span class="font-medium">${{ number_format($price->price_article_once, 2) }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($price->price_article_per_day)
                            <span class="font-medium">${{ number_format($price->price_article_per_day, 2) }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $price->max_placements ?? '∞' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($price->is_public)
                            <span class="text-green-600">✓</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('webmaster.prices.edit', $price) }}"
                           class="text-blue-600 hover:underline text-xs mr-3">{{ __('client.edit') }}</a>
                        <form method="POST" action="{{ route('webmaster.prices.destroy', $price) }}"
                              class="inline" onsubmit="return confirm(__('client.delete_rule_confirm'))">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">{{ __('client.delete') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 text-xs text-gray-400">
            * — розраховано через базову ціну × коефіцієнт
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $prices->links() }}</div>
        @endif
    </div>
</div>
@endsection
