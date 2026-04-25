@extends('webmaster.layouts.app')
@section('title', __('client.pages_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('client.pages_title') }}</h1>

    @if($wpSites->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-400">
            <p>{{ __('client.pages_no_plugin') }}</p>
            <p class="mt-2 text-sm">{{ __('client.pages_install_hint') }}</p>
        </div>
    @else

    {{-- Фільтри --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex flex-col gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.pages_site') }}</label>
                    <select name="site_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">{{ __('client.pages_all_sites') }}</option>
                        @foreach($wpSites as $site)
                            <option value="{{ $site->domain }}" {{ request("site_id") == $site->domain ? 'selected' : '' }}>
                                {{ $site->domain }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.pages_search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('client.pages_search_placeholder') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="checkbox" name="has_anchors" value="1" {{ request('has_anchors') ? 'checked' : '' }} class="rounded">
                    {{ __('client.pages_only_anchors') }}
                </label>
                <div class="flex gap-2 ml-auto">
                    <button type="submit" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100">{{ __('client.filter_btn') }}</button>
                    <a href="{{ route('webmaster.pages.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('client.reset') }}</a>
                </div>
            </div>
        </form>
    </div>

    @if($pages->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-400">
            {{ __('client.pages_not_found') }}
        </div>
    @else

    {{-- Mobile: картки --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($pages as $page)
        @php $count = is_array($page->anchors) ? count($page->anchors) : 0; @endphp
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <a href="{{ route('webmaster.pages.show', $page) }}" class="block hover:text-blue-600 dark:hover:text-blue-400 transition mb-1">
                <div class="font-medium text-gray-900 dark:text-white truncate">{{ $page->title ?: '—' }}</div>
                <div class="text-xs text-blue-500 dark:text-blue-400 truncate">{{ $page->url }}</div>
            </a>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-3">
                <div class="flex items-center gap-2">
                    @if($count > 0)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded">
                            {{ $count }} {{ __('client.pages_anchors') }}
                        </span>
                    @endif
                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">{{ $page->post_type }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>{{ $page->published_at?->format('d.m.Y') ?? '—' }}</span>
                    <span class="flex items-center gap-1">
                        <span>{{ __('client.link_limit') }}:</span>
                        <input type="number" min="1" max="20" value="{{ $page->link_limit }}"
                            data-url="{{ route('webmaster.pages.limit', $page) }}"
                            class="limit-input w-12 text-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded px-1 py-0.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Desktop: таблиця --}}
    <div class="hidden md:block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.pages_col_title') }}</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.pages_col_site') }}</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.pages_anchors') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.pages_col_type') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.pages_col_date') }}</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase">{{ __('client.link_limit') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($pages as $page)
                @php $count = is_array($page->anchors) ? count($page->anchors) : 0; @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="px-6 py-3">
                        <div class="font-medium text-gray-900 dark:text-white truncate max-w-xs">{{ $page->title ?: '—' }}</div>
                        <a href="{{ $page->url }}" target="_blank" class="text-xs text-blue-500 dark:text-blue-400 hover:underline truncate block max-w-xs">{{ $page->url }}</a>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400 dark:text-gray-500">{{ parse_url($page->siteConnection?->wp_url, PHP_URL_HOST) }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($count > 0)
                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-xs">{{ $count }}</span>
                        @else
                            <span class="text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ $page->post_type }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $page->published_at?->format('d.m.Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" min="1" max="20" value="{{ $page->link_limit }}"
                            data-url="{{ route('webmaster.pages.limit', $page) }}"
                            class="limit-input w-14 text-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('webmaster.pages.show', $page) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs">{{ __('client.details') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $pages->links() }}</div>
    </div>
    <div class="mt-4 md:hidden">{{ $pages->links() }}</div>

    @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.limit-input').forEach(input => {
    let timer;
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
    input.addEventListener('click', e => e.stopPropagation());
    input.addEventListener('change', function(e) {
        e.stopPropagation();
        clearTimeout(timer);
        const el = this;
        const newVal = parseInt(el.value);
        if (isNaN(newVal) || newVal < 1 || newVal > 20) return;
        timer = setTimeout(() => {
            fetch(el.dataset.url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ limit: newVal })
            }).then(r => r.json()).then(data => {
                if (data.ok) {
                    el.value = newVal;
                    el.classList.add('border-green-500');
                    setTimeout(() => el.classList.remove('border-green-500'), 1000);
                }
            });
        }, 300);
    });
});
</script>
@endpush

