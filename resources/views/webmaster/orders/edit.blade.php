@extends('webmaster.layouts.app')
@section('title', __('client.edit_placement'))
@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('webmaster.orders.index') }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-sm">&larr; {{ __('client.orders_title') }}</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.edit_placement') }}</h1>
        @php
            $typeLabel = match($link->placement_type) {
                'onclick'        => 'JS Onclick',
                'link'           => 'SEO посилання',
                'article_once', 'article_client'  => __('client.type_article_once'),
                'article_daily', 'article_webmaster' => __('client.type_article_daily'),
                default          => $link->placement_type,
            };
            $typeBadge = match($link->placement_type) {
                'onclick'  => 'bg-purple-100 text-purple-700',
                'link'     => 'bg-gray-100 text-gray-700',
                default    => 'bg-blue-100 text-blue-700',
            };
        @endphp
        <span class="text-xs px-2 py-1 rounded-full {{ $typeBadge }}">{{ $typeLabel }}</span>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    {{-- Інфо --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-5 text-sm text-gray-600 space-y-1">
        <div>{{ __('client.col_campaign') }}: <strong>{{ $link->campaign?->name ?? '—' }}</strong></div>
        <div>{{ __('client.col_domain') }}: <strong>{{ $link->site?->domain ?? '—' }}</strong></div>
        <div>{{ __('client.col_price') }}: <strong>\${{ number_format($link->price_per_day, 2) }}/{{ __('client.day') }}</strong></div>
    </div>

    <form method="POST" action="{{ route('webmaster.orders.update', $link) }}"
          class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf @method('PUT')

        @if($link->placement_type === 'onclick')
        {{-- ── JS ONCLICK ── --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Сторінка донора</label>
            <input type="url" name="donor_url" value="{{ old('donor_url', $link->donor_url) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ціль переходу (target URL)</label>
            <input type="url" name="target_url" value="{{ old('target_url', $link->target_url) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="border border-purple-200 bg-purple-50 rounded-lg p-4 space-y-3">
            <p class="text-xs font-medium text-purple-700">Вкажіть href існуючого зовнішнього посилання на сторінці донора — onclick буде повішений саме на нього</p>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Href посилання для onclick</label>
                <input type="url" name="onclick_href" value="{{ old('onclick_href', $link->onclick_href) }}"
                       placeholder="https://example.com/"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            @if($link->donor_url)
            <div>
                <p class="text-xs font-medium text-gray-600 mb-2">Зовнішні посилання на сторінці:</p>
                <div id="ext-links" class="text-xs text-gray-500 bg-white border border-gray-200 rounded p-2 max-h-48 overflow-y-auto">
                    Завантаження...
                </div>
            </div>
            <script>
            (function(){
                var ajaxUrl = "{{ route('webmaster.ajax.external-links') }}?url={{ urlencode($link->donor_url) }}";
                var currentHref = "{{ $link->onclick_href }}";
                fetch(ajaxUrl)
                    .then(function(r){ return r.json(); })
                    .then(function(data){
                        var container = document.getElementById("ext-links");
                        if (data.error || !data.links || !data.links.length) {
                            container.textContent = data.error || "Зовнішніх посилань не знайдено";
                            return;
                        }
                        container.innerHTML = data.links.map(function(l){
                            var isSelected = currentHref && l.href === currentHref;
                            return '<div class="flex items-center gap-2 py-1.5 border-b border-gray-100 last:border-0 ' + (isSelected ? 'bg-purple-50' : '') + '">' +
                                '<button type="button" onclick="selectHref(this, \'' + l.href.replace(/\'/g, '\\\'')+'\')"'  +
                                ' class="text-xs px-2 py-0.5 border rounded flex-shrink-0 ' + (isSelected ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-600 border-purple-200') + '">Вибрати</button>' +
                                '<div class="min-w-0 flex-1">' +
                                '<div class="text-gray-600 truncate text-xs">' + l.href + '</div>' +
                                '<div class="text-gray-400 text-xs">' + l.text.substring(0,80) + '</div>' +
                                '</div>' +
                                '</div>';
                        }).join("");
                    })
                    .catch(function(e){ document.getElementById("ext-links").textContent = "Помилка завантаження: " + e.message; });
            })();
            function selectHref(btn, href) {
                document.querySelector('[name=onclick_href]').value = href;
                document.querySelectorAll('#ext-links button').forEach(function(b){
                    b.className = b.className.replace('bg-purple-600 text-white border-purple-600', 'bg-purple-50 text-purple-600 border-purple-200');
                });
                btn.className = btn.className.replace('bg-purple-50 text-purple-600 border-purple-200', 'bg-purple-600 text-white border-purple-600');
            }
            </script>
            @endif
        </div>

        @elseif(in_array($link->placement_type, ['article_once', 'article_client', 'article_daily', 'article_webmaster']))
        {{-- ── ARTICLE ── --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Сторінка розміщення (після публікації)</label>
            <input type="url" name="donor_url" value="{{ old('donor_url', $link->donor_url) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ціль посилання в статті</label>
            <input type="url" name="target_url" value="{{ old('target_url', $link->target_url) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Анкор</label>
            <input type="text" name="anchor" value="{{ old('anchor', $link->anchor) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        @else
        {{-- ── SEO LINK ── --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL сторінки розміщення</label>
            <input type="url" name="donor_url" value="{{ old('donor_url', $link->donor_url) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ціль (target URL)</label>
                <input type="url" name="target_url" value="{{ old('target_url', $link->target_url) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Анкор</label>
                <input type="text" name="anchor" value="{{ old('anchor', $link->anchor) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="border border-blue-200 bg-blue-50 rounded-lg p-4 space-y-3">
            <p class="text-xs font-medium text-blue-700">Текст навколо анкору — результат: "[текст до] анкор [текст після]"</p>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Текст ДО анкору</label>
                <input type="text" name="anchor_before" value="{{ old('anchor_before', $link->anchor_before) }}"
                       placeholder="наприклад: Хочете"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Текст ПІСЛЯ анкору</label>
                <input type="text" name="anchor_after" value="{{ old('anchor_after', $link->anchor_after) }}"
                       placeholder="наприклад: на замовлення?"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @if($link->anchor)
            <div class="text-xs text-gray-500 bg-white border border-gray-200 rounded px-3 py-2">
                Попередній перегляд:
                <span class="text-gray-700">{{ $link->anchor_before }} </span>
                <a href="{{ $link->target_url }}" class="text-blue-600 underline">{{ $link->anchor }}</a>
                <span class="text-gray-700"> {{ $link->anchor_after }}</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Спільні поля --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Тип посилання</label>
                <select name="link_type"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="dofollow" {{ $link->link_type === 'dofollow' ? 'selected' : '' }}>dofollow</option>
                    <option value="nofollow" {{ $link->link_type === 'nofollow' ? 'selected' : '' }}>nofollow</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ціна / день ($)</label>
                <input type="number" name="price_per_day" step="0.01" min="0"
                       value="{{ old('price_per_day', $link->price_per_day) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
            <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(['pending' => __('client.status_pending'), 'active' => __('client.status_active'), 'paused' => __('client.status_paused'), 'rejected' => __('client.status_rejected'), 'cancelled' => __('client.status_cancelled')] as $val => $label)
                <option value="{{ $val }}" {{ $link->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Нотатки</label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes', $link->notes) }}</textarea>
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                Зберегти і очистити кеш
            </button>
            <a href="{{ route('webmaster.orders.index') }}"
               class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                Скасувати
            </a>
        </div>
    </form>
</div>
    {{-- Status history --}}
    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('client.history_title') }}</h2>
        </div>
        @if($history->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                {{ __('client.history_empty') }}
            </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($history as $row)
            @php
                $sc = match($row->status) {
                    'active'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                    'paused'   => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                    'pending'  => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                    'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                    'approved' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                    default    => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
                };
            @endphp
            <div class="px-5 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sc }}">
                        {{ __('client.status_' . $row->status) }}
                    </span>
                    @if($row->pause_reason)
                        <span class="text-xs text-yellow-600 dark:text-yellow-500">
                            {{ __('client.pause_reason_' . $row->pause_reason) }}
                        </span>
                    @endif
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ __('client.history_changed_by_' . $row->changed_by) }}
                    </span>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">
                    {{ $row->created_at->format('d.m.Y H:i') }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
@endsection
