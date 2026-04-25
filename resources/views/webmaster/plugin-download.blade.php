@extends('webmaster.layouts.app')
@section('title', __('client.php_client_title'))
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('webmaster.integrations') }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.php_client_title') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6"
         x-data="phpClientDownload('{{ route('webmaster.plugin.search') }}')"
         x-init="search()">

        {{-- Search --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('client.plugin_select_site') }}
            </label>
            <input
                type="text"
                x-model="query"
                x-on:input.debounce.300ms="search"
                x-on:focus="search"
                placeholder="{{ __('client.plugin_search_placeholder') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                       text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- Results --}}
        <div x-show="results.length > 0" class="space-y-2 mb-4" x-cloak>
            <template x-for="site in results" :key="site.uuid">
                <div class="border border-gray-100 dark:border-gray-800 rounded-lg p-3 cursor-pointer transition-colors"
                     :class="selected?.uuid === site.uuid
                        ? 'border-indigo-400 dark:border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                        : 'hover:border-gray-300 dark:hover:border-gray-600'"
                     x-on:click="select(site)">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="site.domain"></span>
                        <span x-show="!site.has_token"
                              class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded">
                            {{ __('client.plugin_no_token') }}
                        </span>
                        <span x-show="site.has_token && selected?.uuid === site.uuid"
                              class="text-xs text-indigo-600 dark:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty --}}
        <div x-show="searched && results.length === 0"
             class="text-sm text-gray-400 dark:text-gray-500 text-center py-4" x-cloak>
            {{ __('client.plugin_no_results') }}
        </div>

        {{-- Download buttons --}}
        <div x-show="selected" x-cloak>
            <div x-show="!selected?.has_token"
                 class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg mb-4 text-sm text-amber-700 dark:text-amber-400">
                {{ __('client.plugin_no_token_hint') }}
                <a :href="'/wm/sites/' + selected?.id + '/tokens'" class="underline ml-1">
                    {{ __('client.plugin_create_token') }}
                </a>
            </div>

            <div x-show="selected?.has_token" class="flex gap-3">
                <a :href="'/wm/plugin/download/' + selected?.uuid + '/modern'"
                   class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-900 dark:bg-white
                          hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900
                          px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PHP 7.4+ ({{ __('client.php_client_modern') }})
                </a>
                <a :href="'/wm/plugin/download/' + selected?.uuid + '/legacy'"
                   class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 dark:border-gray-600
                          text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800
                          px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PHP 5.3+ (Legacy)
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function phpClientDownload(searchUrl) {
    return {
        query:    '',
        results:  [],
        selected: null,
        searched: false,
        async search() {
            const res = await fetch(searchUrl + '?q=' + encodeURIComponent(this.query));
            this.results  = await res.json();
            this.searched = true;
        },
        select(site) {
            this.selected = this.selected?.uuid === site.uuid ? null : site;
        }
    }
}
</script>
@endpush
@endsection
