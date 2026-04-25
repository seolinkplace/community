@php
    $tabs = [
        'links'    => [
            'route' => 'webmaster.prices.links',
            'label' => __('client.prices_tab_links'),
            'icon'  => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
        ],
        'onclick'  => [
            'route' => 'webmaster.prices.onclick',
            'label' => __('client.tab_onclick'),
            'icon'  => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>',
        ],
        'articles' => [
            'route' => 'webmaster.prices.articles',
            'label' => __('client.prices_tab_articles'),
            'icon'  => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        ],
    ];
    $priceLabel = match($priceType) {
        'link'    => __('client.price_per_day'),
        'onclick' => __('client.price_per_day'),
        default   => __('client.price_usd'),
    };
    $priceColumn = match($priceType) {
        'link'    => 'price_link_per_day',
        'onclick' => 'price_onclick_per_day',
        default   => 'price_link_per_day',
    };
@endphp

<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex gap-0 mb-6 border-b border-gray-200 dark:border-gray-800">
        @foreach($tabs as $key => $tab)
            @php $isActive = request()->routeIs('webmaster.prices.' . $key); @endphp
            <a href="{{ route($tab['route'], ['site' => $selectedSiteId]) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                      {{ $isActive
                          ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white'
                          : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                {!! $tab['icon'] !!}
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-4">

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.prices_site') }}</label>
                <form method="GET">
                    <select name="site_id" onchange="this.form.submit()"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ $selectedSiteId == $site->id ? 'selected' : '' }}>
                                {{ $site->domain }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('client.prices_bulk_title') }}</h3>
                <form method="POST" action="{{ route('webmaster.prices.bulk') }}">
                    @csrf
                    <input type="hidden" name="site_id" value="{{ $selectedSiteId }}">
                    <input type="hidden" name="price_type" value="{{ $priceType }}">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_depth') }}</label>
                            <select name="scope_depth"
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                @foreach($depthStats as $depth => $stat)
                                    <option value="{{ $depth }}">{{ __('client.prices_depth_label') }} {{ $depth }} ({{ is_array($stat) ? ($stat['total'] ?? 0) : (is_object($stat) ? $stat->total : 0) }} стор.)</option>
                                @endforeach
                                @if(empty($depthStats))
                                    <option value="1">{{ __('client.depth_1') }}</option>
                                    <option value="2">{{ __('client.depth_2') }}</option>
                                    <option value="3">{{ __('client.depth_3') }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $priceLabel }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                                <input type="number" name="price_value" id="bulk_price_day" step="0.01" min="0.01" placeholder="0.00"
                                       oninput="syncBulkMonthly(this.value)"
                                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                        </div>
                        @if(in_array($priceType, ['link','onclick']))
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_per_month') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                                <input type="number" id="bulk_price_month" step="0.01" min="0" placeholder="0.00"
                                       oninput="syncBulkDaily(this.value)"
                                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('client.prices_divided_30') }}</p>
                        </div>
                        @endif
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" name="is_public" value="1" checked class="rounded border-gray-300 dark:border-gray-600">
                            {{ __('client.prices_public') }}
                        </label>
                        <button type="submit"
                                class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                            Встановити для всіх
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('client.prices_add_rule') }}</h3>
                <form method="POST" action="{{ route('webmaster.prices.store') }}">
                    @csrf
                    <input type="hidden" name="site_id" value="{{ $selectedSiteId }}">
                    <input type="hidden" name="price_type" value="{{ $priceType }}">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_rule_type') }}</label>
                            <select name="scope_type" id="new_scope_type" onchange="toggleScopeFields()"
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                <option value="site_default">{{ __('client.scope_site_default') }}</option>
                                <option value="depth">{{ __('client.scope_depth') }}</option>
                                <option value="url">{{ __('client.scope_url') }}</option>
                                <option value="url_client">{{ __('client.scope_url_client') }}</option>
                            </select>
                        </div>
                        <div id="new_field_depth" class="hidden">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_level') }}</label>
                            <select name="scope_depth"
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                <option value="0">0 — Головна</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div id="new_field_url" class="hidden">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">URL</label>
                            <input type="url" name="scope_url" placeholder="https://..."
                                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                        </div>
                        <div id="new_field_client" class="hidden">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.col_client') }}</label>
                            <select name="client_id"
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                <option value="">{{ __('client.select_client') }}</option>
                                @forelse($clients as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->email }}</option>
                                @empty
                                    <option value="" disabled>{{ __('client.no_clients_yet') }}</option>
                                @endforelse
                            </select>
                            <p class="text-xs text-gray-400 mt-1">{{ __('client.url_client_hint') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $priceLabel }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                                <input type="number" name="price_per_day" id="new_price_day" step="0.01" min="0" placeholder="0.00"
                                       oninput="syncMonthly(this.value)"
                                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                        </div>
                        @if(in_array($priceType, ['link','onclick']))
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_per_month') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                                <input type="number" id="new_price_month" step="0.01" min="0" placeholder="0.00"
                                       oninput="syncDaily(this.value)"
                                       class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-7 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('client.prices_divided_30') }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.prices_max_placements') }}</label>
                            <input type="number" name="max_placements" min="1" placeholder="∞"
                                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                        </div>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <input type="checkbox" name="is_public" value="1" checked class="rounded border-gray-300 dark:border-gray-600">
                                {{ __('client.prices_public_short') }}
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <input type="checkbox" name="adult_allowed" value="1" class="rounded border-gray-300 dark:border-gray-600">
                                Adult
                            </label>
                        </div>
                        <button type="submit"
                                class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                            Зберегти
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Правила
                        <span class="text-gray-400 font-normal ml-1">({{ $prices->total() }})</span>
                    </h2>
                    <span class="text-xs text-gray-400">{{ __('client.prices_priority') }}</span>
                </div>

                @if($prices->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-400 text-sm">
                        {{ __('client.prices_no_rules') }}
                    </div>
                @else
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Scope</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">{{ $priceLabel }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">{{ __('client.prices_max_short') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($prices as $price)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 group relative">
                            <td class="px-4 py-2.5">
                                @php
                                    $scopeStyles = [
                                        'site_default' => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                                        'depth'        => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                                        'url'          => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                                        'url_client'   => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $scopeStyles[$price->scope_type] }}">
                                    {{ __('client.' . \App\Models\PagePrice::SCOPE_LABELS[$price->scope_type]) }}
                                </span>
                                <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">
                                    {{ $price->getScopeLabel() }}
                                    @if($price->client) <span class="text-gray-500">({{ $price->client->email }})</span> @endif
                                    @if($price->adult_allowed) <span class="text-gray-500">· {{ __('client.adult_label') }}</span> @endif
                                </div>
                            </td>
                            @php $val = $price->$priceColumn; @endphp
                            <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-gray-100">
                                {{ $val ? '$' . number_format($val, 4) : '—' }}
                            </td>
                            <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400">{{ $price->max_placements ?? '∞' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <form method="POST" action="{{ route('webmaster.prices.update', $price) }}"
                                      class="hidden group-hover:flex items-center gap-1.5 justify-end">
                                    @csrf @method('PUT')
                                    <input type="number" name="price_value" step="0.0001" min="0"
                                           value="{{ $val }}"
                                           class="w-24 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 text-xs text-gray-900 dark:text-gray-100">
                                    <button class="text-xs text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">OK</button>
                                </form>
                                <form method="POST" action="{{ route('webmaster.prices.destroy', $price) }}"
                                      class="inline" onsubmit="return confirm(__('client.prices_delete_confirm'))">
                                    @csrf @method('DELETE')
                                    <button style="color:#ef4444;font-size:14px;cursor:pointer;background:none;border:none;padding:4px 8px;">✕</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">{{ $prices->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleScopeFields() {
    const type = document.getElementById('new_scope_type').value;
    document.getElementById('new_field_depth').classList.toggle('hidden', type !== 'depth');
    document.getElementById('new_field_url').classList.toggle('hidden', !['url','url_client'].includes(type));
    document.getElementById('new_field_client').classList.toggle('hidden', type !== 'url_client');
}
function syncMonthly(val) {
    const m = document.getElementById('new_price_month');
    if (m) m.value = val ? (parseFloat(val) * 30).toFixed(2) : '';
}
function syncDaily(val) {
    const d = document.getElementById('new_price_day');
    if (d) d.value = val ? (parseFloat(val) / 30).toFixed(4) : '';
}
function syncBulkMonthly(val) {
    const m = document.getElementById('bulk_price_month');
    if (m) m.value = val ? (parseFloat(val) * 30).toFixed(2) : '';
}
function syncBulkDaily(val) {
    const d = document.getElementById('bulk_price_day');
    if (d) d.value = val ? (parseFloat(val) / 30).toFixed(4) : '';
}
</script>
