@extends('client.layouts.app')
@section('title', $site->domain)

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="mb-6">
        <a href="{{ route('client.catalog.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('client.back_catalog') }}</a>
    </div>

    {{-- Інфо про сайт --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $site->domain }}</h1>
                @if($site->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $site->description }}</p>
                @endif
            </div>
            @if($site->is_adult)
                <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-1 rounded">18+</span>
            @endif
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mt-4">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">DR</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $site->dr ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.traffic') }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $site->traffic ? number_format($site->traffic) : '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">Spam</span>
                <span class="font-semibold {{ $site->spam_score !== null && $site->spam_score > 30 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">{{ $site->spam_score ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.pages_in_system') }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $site->pages_count ?: '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.niche') }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $site->niche ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.language') }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $site->language ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.site_domain_registered') }}</span>
                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                    {{ $site->domain_registered_at ? $site->domain_registered_at->format('Y-m-d') : '—' }}
                </span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 {{ $site->domain_expires_at && $site->domain_expires_at->diffInDays(now()) < 60 ? 'border border-red-300 dark:border-red-800' : '' }}">
                <span class="text-xs text-gray-400 block mb-1">{{ __('client.site_domain_expires') }}</span>
                <span class="font-semibold text-sm {{ $site->domain_expires_at && $site->domain_expires_at->diffInDays(now()) < 60 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                    {{ $site->domain_expires_at ? $site->domain_expires_at->format('Y-m-d') : '—' }}
                    @if($site->domain_expires_at && $site->domain_expires_at->diffInDays(now()) < 60)
                    <span class="text-xs text-red-400 block font-normal">{{ __('client.domain_expires_soon') }}</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Таби --}}
    <div class="flex gap-0 mb-6 border-b border-gray-200 dark:border-gray-800">
        @foreach(['link' => __('client.tab_links'), 'onclick' => __('client.tab_onclick'), 'article' => __('client.tab_articles'), 'write' => __('client.tab_write')] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'search' => '']) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                      {{ $tab === $key
                          ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white'
                          : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Посилання / Onclick --}}
    @if(in_array($tab, ['link', 'onclick']))

        {{-- Пошук --}}
        <form method="GET" class="mb-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="{{ __('client.search_placeholder') }}"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
            </div>
        </form>

        {{-- Список сторінок --}}
        @if($paginator && $paginator->count() > 0)
        <div class="space-y-2">
            @foreach($paginator as $page)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $page->title ?: $page->url }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $page->url }}</p>
                        @if($page->anchors)
                            @php $anchors = is_string($page->anchors) ? json_decode($page->anchors, true) : $page->anchors; @endphp
                            @if($anchors && count($anchors) > 0)
                                <p class="text-xs text-gray-400 mt-1">{{ count($anchors) }} {{ __('client.anchors_count') }}</p>
                            @endif
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        @if($page->resolved_price)
                            @if($tab === 'onclick')
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($page->resolved_price, 4) }}<span class="text-xs font-normal text-gray-400">/{{ __('client.per_click') }}</span></p>
                            @else
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($page->resolved_price, 4) }}<span class="text-xs font-normal text-gray-400">{{ __('client.per_day') }}</span></p>
                            <p class="text-xs text-gray-400">${{ number_format($page->resolved_price * 30, 2) }}/{{ __('client.per_month') }}</p>
                            @endif
                        @else
                            <p class="text-xs text-gray-400">{{ __('client.no_price') }}</p>
                        @endif
                        @if($page->resolved_price)
                        <button onclick="openOrderModal('{{ $page->url }}', '{{ $tab }}', {{ $page->resolved_price }})"
                                class="mt-2 bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                            Замовити
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $paginator->links() }}</div>
        @elseif($search)
            <div class="text-center py-12 text-gray-400 text-sm">{{ __('client.not_found_search') }} "{{ $search }}"</div>
        @else
            <div class="text-center py-12 text-gray-400 text-sm">{{ __('client.pages_not_found') }}</div>
        @endif

    @endif

    {{-- Статті --}}
    @if($tab === 'write')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Write only --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('client.write_only_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('client.write_only_desc') }}</p>
            @if($articleWebmasterPrice && $articleWebmasterPrice->price_article_once)
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-4">${{ number_format($articleWebmasterPrice->price_article_once, 2) }} <span class="text-sm font-normal text-gray-400">{{ __('client.once') }}</span></p>
                <form method="POST" action="{{ route('client.catalog.order', $site) }}">
                    @csrf
                    <input type="hidden" name="placement_type" value="article_once">
                    <input type="hidden" name="order_type" value="write_only">
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.article_topic_label') }}</label>
                        <input type="text" name="article_topic" placeholder="{{ __('client.article_topic_ph') }}" required
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                    </div>
                    <button type="submit" class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                        {{ __('client.order_write_only') }}
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-400">{{ __('client.no_write_price') }}</p>
            @endif
        </div>
    </div>
    @endif

    @if($tab === 'article')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Client provides text --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('client.article_client_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('client.article_client_desc') }}</p>
            @if($articleClientPrice && $articleClientPrice->price_article_once)
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    ${{ number_format($articleClientPrice->price_article_once, 2) }}
                    <span class="text-sm font-normal text-gray-400">{{ __('client.once') }}</span>
                </p>
                <a href="{{ route('client.articles.create', ['site' => $site->uuid]) }}"
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition-colors">
                    {{ __('client.articles_place_here') }}
                </a>
            @else
                <p class="text-sm text-gray-400">{{ __('client.price_not_set') }}</p>
            @endif
        </div>

        {{-- Webmaster writes --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('client.article_wm_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('client.article_wm_desc') }}</p>
            @if($articleClientPrice && $articleClientPrice->price_article_wm_once)
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    ${{ number_format($articleClientPrice->price_article_wm_once, 2) }}
                    <span class="text-sm font-normal text-gray-400">{{ __('client.once') }}</span>
                </p>
                <button onclick="openWmWriteModal()"
                        class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                    {{ __('client.order_wm_write') }}
                </button>
            @else
                <p class="text-sm text-gray-400">{{ __('client.no_wm_write_price') }}</p>
            @endif
        </div>

    </div>
    @endif

</div>

{{-- Webmaster writes modal --}}
<div id="wmWriteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('client.article_wm_title') }}</h3>
            <button onclick="closeWmWriteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('client.articles.order-writing') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="site_uuid" value="{{ $site->uuid }}">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.article_brief_label') }}</label>
                <textarea name="brief" rows="5" required
                          placeholder="{{ __('client.article_brief_ph') }}"
                          class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.article_notes_label') }}</label>
                <textarea name="notes" rows="2"
                          placeholder="{{ __('client.article_notes_ph') }}"
                          class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            @if($articleClientPrice && $articleClientPrice->price_article_wm_once)
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('client.order_will_charge', ['price' => number_format($articleClientPrice->price_article_wm_once, 2)]) }}
            </p>
            @endif
            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition-colors">
                    {{ __('client.order_wm_write') }}
                </button>
                <button type="button" onclick="closeWmWriteModal()"
                        class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium py-2.5 rounded-lg transition-colors">
                    {{ __('client.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

</div>

{{-- Модальне вікно для посилання/onclick --}}
<div id="orderModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white" id="modalTitle">{{ __('client.modal_title') }}</h3>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('client.catalog.order', $site) }}">
            @csrf
            <input type="hidden" name="placement_type" id="modal_placement_type">
            <input type="hidden" name="donor_url" id="modal_donor_url">
            <input type="hidden" name="order_type" id="modal_order_type" value="place_only">
            <div class="space-y-3">
                <div id="modal_price_info" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm text-gray-600 dark:text-gray-300"></div>

                <div id="modal_target_url_wrap">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.modal_target_url') }}</label>
                    <input type="url" name="target_url" placeholder="https://..."
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                </div>

                <div id="modal_anchor_wrap">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.modal_anchor_before') }}</label>
                    <input type="text" name="anchor_before" placeholder="{{ __('client.modal_anchor_before_ph') }}"
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 mb-2">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.modal_anchor') }}</label>
                    <input type="text" name="anchor" placeholder="{{ __('client.modal_anchor_ph') }}"
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 mb-2">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.modal_anchor_after') }}</label>
                    <input type="text" name="anchor_after" placeholder="{{ __('client.modal_anchor_after_ph') }}"
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                </div>

                <div id="modal_onclick_wrap" class="hidden">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.modal_onclick_url') }}</label>
                    <input type="url" name="onclick_href" placeholder="https://..."
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                </div>

                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Тип посилання</label>
                    <select name="link_type"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                        <option value="dofollow">Dofollow</option>
                        <option value="nofollow">Nofollow</option>
                    </select>
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                    Підтвердити замовлення
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Модальне вікно для статті --}}
<div id="articleModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('client.modal_article_title') }}</h3>
            <button onclick="closeArticleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('client.catalog.order', $site) }}">
            @csrf
            <input type="hidden" name="placement_type" id="article_placement_type">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Тема або опис статті</label>
                    <textarea name="article_topic" rows="3" placeholder="Опишіть тему статті..."
                              class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100 resize-none"></textarea>
                </div>
                <button type="submit"
                        class="w-full bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                    Підтвердити замовлення
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openOrderModal(url, type, price) {
    document.getElementById('modal_placement_type').value = type;
    document.getElementById('modal_donor_url').value = url;
    document.getElementById('modalTitle').textContent = type === 'onclick' ? 'Замовити Onclick' : 'Замовити посилання';
    document.getElementById('modal_price_info').textContent = 'Сторінка: ' + url + ' · $' + price.toFixed(4) + '/день ($' + (price * 30).toFixed(2) + '/міс)';

    const isOnclick = type === 'onclick';
    document.getElementById('modal_target_url_wrap').classList.toggle('hidden', isOnclick);
    document.getElementById('modal_anchor_wrap').classList.toggle('hidden', isOnclick);
    document.getElementById('modal_onclick_wrap').classList.toggle('hidden', !isOnclick);

    document.getElementById('orderModal').classList.remove('hidden');
}
function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
}
function openWmWriteModal() {
    document.getElementById('wmWriteModal').classList.remove('hidden');
}
function closeWmWriteModal() {
    document.getElementById('wmWriteModal').classList.add('hidden');
}
function openWmWriteModal() {
    document.getElementById('wmWriteModal').classList.remove('hidden');
}
function closeWmWriteModal() {
    document.getElementById('wmWriteModal').classList.add('hidden');
}
function openArticleModal(type) {
    document.getElementById('article_placement_type').value = type;
    document.getElementById('articleModal').classList.remove('hidden');
}
function closeArticleModal() {
    document.getElementById('articleModal').classList.add('hidden');
}
// Закрити по кліку на фон
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderModal();
});
document.getElementById('articleModal').addEventListener('click', function(e) {
    if (e.target === this) closeArticleModal();
});
</script>
@endsection
