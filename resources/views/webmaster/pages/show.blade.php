@extends('webmaster.layouts.app')
@section('title', $page->title ?: __('client.page_no_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('webmaster.pages.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $page->title ?: __('client.page_no_title') }}</h1>
            <a href="{{ $page->url }}" target="_blank" class="text-sm text-blue-500 dark:text-blue-400 hover:underline truncate block">{{ $page->url }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Ліва колонка --}}
        <div class="flex flex-col gap-6">

            {{-- Інформація --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.page_info') }}</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('client.pages_col_type') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $page->post_type }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('client.pages_col_date') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $page->published_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('client.page_synced') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $page->synced_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('client.link_limit') }}</dt>
                        <dd class="flex items-center gap-2">
                            <input type="number" min="1" max="20" value="{{ $page->link_limit }}"
                                id="limit-input"
                                data-url="{{ route('webmaster.pages.limit', $page) }}"
                                class="w-16 text-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span id="limit-status" class="text-xs text-green-500 hidden">✓</span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Анкори --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.page_anchors') }} ({{ count($page->anchors ?? []) }})</h2>
                @if(empty($page->anchors))
                    <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.page_no_anchors') }}</p>
                @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($page->anchors as $anchor)
                        <div class="text-sm border-b border-gray-100 dark:border-gray-800 pb-2">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $anchor['text'] }}</div>
                            <a href="{{ $anchor['href'] }}" target="_blank" class="text-xs text-blue-500 dark:text-blue-400 hover:underline truncate block">{{ $anchor['href'] }}</a>
                            @if(isset($anchorClicks[$anchor['href']]))
                                <span class="text-xs text-green-600 dark:text-green-400">{{ $anchorClicks[$anchor['href']]->total_clicks }} {{ __('client.clicks_30d') }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Права колонка: Ціни --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 h-fit">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('client.page_prices') }}</h2>
                <a href="{{ route('webmaster.prices.links', ['site' => $siteId]) }}"
                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('client.prices_all_rules') }} →</a>
            </div>

            @php
                $priceTypes = [
                    'link' => ['label' => __('client.prices_tab_links'), 'col' => 'price_link_per_day', 'month_col' => 'price_link_per_month'],
                ];
                if ($page->post_type !== 'homepage') {
                    $priceTypes['onclick'] = ['label' => __('client.tab_onclick'), 'col' => 'price_onclick_per_day', 'month_col' => 'price_onclick_per_month'];
                    $priceTypes['article'] = ['label' => __('client.prices_tab_articles'), 'col' => 'price_link_per_day', 'month_col' => 'price_link_per_month'];
                }
            @endphp

            <div class="space-y-5">
                @foreach($priceTypes as $type => $cfg)
                @php
                    $price    = $urlPrices[$type] ?? null;
                    $valMonth = $price?->{$cfg['month_col']} ?? null;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cfg['label'] }}</span>
                        @if($valMonth)
                            <span id="price-display-{{ $type }}" class="text-xs text-green-600 dark:text-green-400 font-medium">${{ number_format($valMonth, 2) }}/{{ __('client.month') }}</span>
                        @else
                            <span id="price-display-{{ $type }}" class="text-xs text-gray-400 dark:text-gray-500 italic">{{ __('client.prices_inherited') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" min="0.01" step="0.01"
                               value="{{ $valMonth ? number_format($valMonth, 2) : '' }}"
                               placeholder="{{ __('client.price_per_month_ph') }}"
                               id="price-month-{{ $type }}"
                               class="flex-1 min-w-0 text-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button"
                                id="price-btn-{{ $type }}"
                                data-type="{{ $type }}"
                                data-save="{{ __('client.save') }}"
                                data-saved="{{ __('client.saved') }}"
                                data-url="{{ route('webmaster.prices.store') }}"
                                data-site="{{ $siteId }}"
                                data-page-url="{{ $page->url }}"
                                onclick="savePagePrice(this)"
                                class="shrink-0 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-700 dark:hover:bg-gray-100 transition">
                            {{ __('client.save') }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-500 mt-6">{{ __('client.prices_url_override_hint') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('client.prices_daily_billing_hint') }}</p>
        </div>

    </div>
</div>

@push('scripts')
<script>
function savePagePrice(btn) {
    const type       = btn.dataset.type;
    const url        = btn.dataset.url;
    const siteId     = btn.dataset.site;
    const pageUrl    = btn.dataset.pageUrl;
    const monthInput = document.getElementById('price-month-' + type);
    const display    = document.getElementById('price-display-' + type);

    if (!monthInput || !monthInput.value || parseFloat(monthInput.value) <= 0) return;

    const pricePerMonth = parseFloat(monthInput.value);
    const originalText  = btn.textContent.trim();
    btn.textContent = '...';
    btn.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            site_id:         siteId,
            price_type:      type === 'article' ? 'article_client' : type,
            scope_type:      'url',
            scope_url:       pageUrl,
            price_per_month: pricePerMonth,
            is_public:       1,
        })
    })
    .then(r => r.json())
    .then(() => {
        // Оновити відображення ціни
        if (display) {
            display.textContent = '$' + pricePerMonth.toFixed(2) + '/{{ __("client.month") }}';
            display.className = 'text-xs text-green-600 dark:text-green-400 font-medium';
        }
        btn.textContent = '✓ ' + btn.dataset.saved;
        btn.style.background = '#16a34a';
        btn.style.color = '#fff';
        setTimeout(() => {
            btn.textContent = btn.dataset.save;
            btn.style.background = '';
            btn.style.color = '';
            btn.disabled = false;
        }, 2000);
    })
    .catch(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

const limitInput = document.getElementById('limit-input');
const limitStatus = document.getElementById('limit-status');
if (limitInput) {
    let timer;
    limitInput.addEventListener('change', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetch(limitInput.dataset.url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ limit: limitInput.value })
            }).then(r => r.json()).then(data => {
                if (data.ok) {
                    limitInput.classList.add('border-green-500');
                    limitStatus.classList.remove('hidden');
                    setTimeout(() => {
                        limitInput.classList.remove('border-green-500');
                        limitStatus.classList.add('hidden');
                    }, 2000);
                }
            });
        }, 400);
    });
}
</script>
@endpush
@endsection
