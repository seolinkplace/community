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
    $articleTypes = [
        'article_client'    => __('client.article_client_provides'),
        'article_webmaster' => __('client.article_wm_writes'),
    ];
    $selectedArticleType = request('article_type', 'article_client');
@endphp

<div class="max-w-6xl mx-auto py-8 px-4">

    {{-- Таби --}}
    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
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
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Підтаби --}}
    <div class="flex gap-2 mb-6">
        @foreach($articleTypes as $type => $label)
            <a href="{{ request()->fullUrlWithQuery(['article_type' => $type]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors
                      {{ $selectedArticleType === $type
                          ? 'bg-gray-900 text-white border-gray-900'
                          : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Ліва колонка --}}
        <div class="space-y-4">

            {{-- Фільтр сайту --}}
            <div class="bg-white rounded-xl shadow p-4">
                <label class="block text-xs font-medium text-gray-500 mb-2">{{ __('client.site_label') }}</label>
                <form method="GET">
                    <input type="hidden" name="article_type" value="{{ $selectedArticleType }}">
                    <select name="site_id" onchange="this.form.submit()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ $selectedSiteId == $site->id ? 'selected' : '' }}>
                                {{ $site->domain }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Форма ціни --}}
            <div class="bg-white rounded-xl shadow p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">
                    {{ $articleTypes[$selectedArticleType] }}
                </h3>
                <p class="text-xs text-gray-400 mb-3">
                    @if($selectedArticleType === 'article_client')
                        {{ __('client.article_client_desc_wm') }}
                    @else
                        {{ __('client.article_wm_desc') }}
                    @endif
                </p>

                <form method="POST" action="{{ route('webmaster.prices.store') }}">
                    @csrf
                    <input type="hidden" name="site_id" value="{{ $selectedSiteId }}">
                    <input type="hidden" name="price_type" value="{{ $selectedArticleType }}">
                    <input type="hidden" name="scope_type" value="site_default">

                    <div class="space-y-3">
                        {{-- Тип правила (для кастомних налаштувань) --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Застосувати до</label>
                            <select name="scope_type" id="art_scope_type" onchange="toggleArtScope()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="site_default">Весь сайт (дефолт)</option>
                                <option value="url">Конкретна сторінка</option>
                                <option value="url_client">{{ __('client.scope_url_client_full') }}</option>
                            </select>
                        </div>

                        <div id="art_field_url" class="hidden">
                            <label class="block text-xs text-gray-500 mb-1">URL сторінки</label>
                            <input type="url" name="scope_url" placeholder="https://..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div id="art_field_client" class="hidden">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('client.client_label') }}</label>
                            <select name="client_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="">— оберіть —</option>
                                @foreach(\App\Models\Client::orderBy('email')->get() as $client)
                                    <option value="{{ $client->id }}">{{ $client->email }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип оплати --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-2">{{ __('client.billing_type') }}</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="billing_type" value="once" checked
                                           onchange="toggleArtBilling(this.value)">
                                    Разова
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="billing_type" value="daily"
                                           onchange="toggleArtBilling(this.value)">
                                    Поденна
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1" id="art_price_label">{{ __('client.price_once_usd') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                                <input type="number" name="price_per_day" step="0.01" min="0"
                                       placeholder="0.00"
                                       class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('client.prices_max_placements') }}</label>
                            <input type="number" name="max_placements" min="1" placeholder="∞"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-gray-600">
                                <input type="checkbox" name="is_public" value="1" checked
                                       class="rounded border-gray-300 text-blue-600">
                                Публічна
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-gray-600">
                                <input type="checkbox" name="adult_allowed" value="1"
                                       class="rounded border-gray-300 text-red-500">
                                Adult (18+)
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium py-2.5 rounded-lg">
                            Зберегти ціну
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Права колонка: таблиця --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 text-sm">
                        {{ $articleTypes[$selectedArticleType] }}
                        <span class="text-gray-400 font-normal">({{ $prices->total() }})</span>
                    </h2>
                    <span class="text-xs text-gray-400">Пріоритет: клієнт &gt; URL &gt; дефолт</span>
                </div>

                @if($prices->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                        Ціну ще не встановлено. Заповніть форму зліва.
                    </div>
                @else
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Scope</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Разова</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">/ день</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">{{ __('client.prices_max_placements') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Публ.</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($prices as $price)
                        @php
                            $colors = [
                                'site_default' => 'bg-gray-100 text-gray-600',
                                'depth'        => 'bg-purple-100 text-purple-700',
                                'url'          => 'bg-blue-100 text-blue-700',
                                'url_client'   => 'bg-green-100 text-green-700',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 group">
                            <td class="px-4 py-2">
                                <span class="inline-block px-1.5 py-0.5 rounded text-xs {{ $colors[$price->scope_type] }}">
                                    {{ __('client.' . \App\Models\PagePrice::SCOPE_LABELS[$price->scope_type]) }}
                                </span>
                                <div class="text-xs text-gray-400 truncate max-w-xs mt-0.5">
                                    {{ $price->getScopeLabel() }}
                                    @if($price->client) <span class="text-green-600">({{ $price->client->email }})</span> @endif
                                    @if($price->adult_allowed) <span class="text-red-500">18+</span> @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right font-medium">
                                {{ $price->price_article_once ? '$'.number_format($price->price_article_once, 2) : '—' }}
                            </td>
                            <td class="px-4 py-2 text-right font-medium">
                                {{ $price->price_article_per_day ? '$'.number_format($price->price_article_per_day, 2) : '—' }}
                            </td>
                            <td class="px-4 py-2 text-center text-gray-500">{{ $price->max_placements ?? '∞' }}</td>
                            <td class="px-4 py-2 text-center">{{ $price->is_public ? '✓' : '—' }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('webmaster.prices.destroy', $price) }}"
                                      class="inline" onsubmit="return confirm(__('client.prices_delete_confirm'))">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs opacity-0 group-hover:opacity-100">✕</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100">{{ $prices->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleArtScope() {
    const type = document.getElementById('art_scope_type').value;
    document.getElementById('art_field_url').classList.toggle('hidden', !['url','url_client'].includes(type));
    document.getElementById('art_field_client').classList.toggle('hidden', type !== 'url_client');
}
function toggleArtBilling(val) {
    document.getElementById('art_price_label').textContent =
        val === 'once' ? __('client.price_once') : 'Ціна / день (USD)';
}
</script>
