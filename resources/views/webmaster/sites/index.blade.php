@extends('webmaster.layouts.app')
@section('title', __('client.wm_sites_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.wm_sites_title') }}</h1>
        <a href="{{ route('webmaster.sites.create') }}"
           class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-100">
            {{ __('client.wm_add_site') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($sites->count() === 0)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-400">
            <p>{{ __('client.wm_no_sites') }}</p>
            <a href="{{ route('webmaster.sites.create') }}" class="mt-4 inline-block text-blue-600 hover:underline text-sm">{{ __('client.add_first_site') }}</a>
        </div>
    @else
{{-- Desktop table --}}
    <div class="hidden md:block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.col_domain') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">DR</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.traffic_monthly') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Spam</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.pages_in_system') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.col_price') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.active_links') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.articles') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.site_domain_registered') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($sites as $site)
                @php $langs = $site->siteLanguages->pluck('language_code')->toArray(); @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                    {{-- Domain + badges --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-gray-400 flex-shrink-0"><x-platform-icon :platform="$site->platform_type ?? 'website'" /></span>
                            <div class="min-w-0">
                                <a href="{{ route('webmaster.sites.edit', $site) }}"
                                   class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block">
                                    {{ $site->platform_type !== 'website' && $site->platform_url ? $site->platform_url : $site->domain }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    @if($site->niche)
                                    <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $site->niche }}</span>
                                    @endif
                                    @foreach($langs as $code)
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 rounded">{{ strtoupper($code) }}</span>
                                    @endforeach
                                    @if($site->verified_at)
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400">✓</span>
                                    @else
                                    <span class="text-xs text-yellow-500">!</span>
                                    @endif
                                    <span class="text-xs {{ $site->visibility === 'public' ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                        {{ $site->visibility === 'public' ? __('client.visibility_public') : __('client.visibility_private') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ $site->dr ?? '—' }}</td>
                    <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ $site->traffic ? number_format($site->traffic) : '—' }}</td>
                    <td class="px-3 py-3 text-center {{ $site->spam_score !== null && $site->spam_score > 30 ? 'text-red-500 font-medium' : 'text-gray-700 dark:text-gray-300' }}">{{ $site->spam_score ?? '—' }}</td>
                    <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ $site->pages_count ?: '—' }}</td>
                    <td class="px-3 py-3 text-center font-medium text-gray-900 dark:text-white">{{ $site->price ? '$'.$site->price : '—' }}</td>
                    <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ $site->campaign_links_count }}</td>
                    <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ $site->articles_count }}</td>
                    <td class="px-3 py-3 text-center text-gray-500 text-xs">
                        @if($site->domain_registered_at)
                            {{ $site->domain_registered_at->format('Y') }}
                            @if($site->domain_expires_at)
                            <span class="{{ $site->domain_expires_at->diffInDays(now()) < 60 ? 'text-red-500' : 'text-gray-400' }}">
                                → {{ $site->domain_expires_at->format('Y-m-d') }}
                            </span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3 justify-end whitespace-nowrap">
                            <a href="{{ route('webmaster.sites.tokens.index', $site) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('client.col_tokens') }}</a>
                            <a href="{{ route('webmaster.sites.edit', $site) }}" class="text-xs text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('client.edit') }}</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($sites as $site)
        @php $langs = $site->siteLanguages->pluck('language_code')->toArray(); @endphp
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-gray-400 flex-shrink-0"><x-platform-icon :platform="$site->platform_type ?? 'website'" /></span>
                    <a href="{{ route('webmaster.sites.edit', $site) }}" class="font-bold text-gray-900 dark:text-white truncate">
                        {{ $site->platform_type !== 'website' && $site->platform_url ? $site->platform_url : $site->domain }}
                    </a>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    @if($site->verified_at)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">✓</span>
                    @else
                    <a href="{{ route('webmaster.sites.edit', $site) }}#verify" class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">!</a>
                    @endif
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $site->visibility === 'public' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                        {{ $site->visibility === 'public' ? __('client.visibility_public') : __('client.visibility_private') }}
                    </span>
                </div>
            </div>
            {{-- Metrics grid --}}
            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800 border-b border-gray-100 dark:border-gray-800">
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $site->dr ?? '—' }}</div>
                    <div class="text-xs text-gray-400">DR</div>
                </div>
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $site->traffic ? number_format($site->traffic) : '—' }}</div>
                    <div class="text-xs text-gray-400">{{ __('client.traffic_monthly') }}</div>
                </div>
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-sm {{ $site->spam_score !== null && $site->spam_score > 30 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">{{ $site->spam_score ?? '—' }}</div>
                    <div class="text-xs text-gray-400">Spam</div>
                </div>
            </div>
            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800 border-b border-gray-100 dark:border-gray-800">
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $site->price ? '$'.$site->price : '—' }}</div>
                    <div class="text-xs text-gray-400">{{ __('client.col_price') }}</div>
                </div>
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $site->pages_count ?: '—' }}</div>
                    <div class="text-xs text-gray-400">{{ __('client.pages_in_system') }}</div>
                </div>
                <div class="px-3 py-2.5 text-center">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $site->campaign_links_count }}</div>
                    <div class="text-xs text-gray-400">{{ __('client.active_links') }}</div>
                </div>
            </div>
            <div class="px-4 py-2 text-xs text-gray-400 flex flex-wrap gap-2 border-b border-gray-100 dark:border-gray-800">
                @if($site->niche)<span class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $site->niche }}</span>@endif
                @foreach($langs as $code)<span class="text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 rounded">{{ strtoupper($code) }}</span>@endforeach
                <span>{{ __('client.site_domain_registered') }}: {{ $site->domain_registered_at ? $site->domain_registered_at->format('Y-m-d') : '—' }}</span>
                <span class="{{ $site->domain_expires_at && $site->domain_expires_at->diffInDays(now()) < 60 ? 'text-red-500' : '' }}">{{ __('client.site_domain_expires') }}: {{ $site->domain_expires_at ? $site->domain_expires_at->format('Y-m-d') : '—' }}</span>
            </div>
            {{-- Actions --}}
            <div class="flex items-center gap-4 px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50">
                <a href="{{ route('webmaster.sites.tokens.index', $site) }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ __('client.col_tokens') }}</a>
                <a href="{{ route('webmaster.sites.edit', $site) }}" class="text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900">{{ __('client.edit') }}</a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $sites->links() }}</div>
    @endif
</div>
@endsection
