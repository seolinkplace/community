@extends('webmaster.layouts.app')
@section('title', __('nav.dashboard'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('nav.dashboard') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ __('client.wm_welcome') }} {{ \App\Helpers\AuthHelper::webmaster()->name }}</p>
    </div>

    {{-- First post reminder --}}
    @if(isset($socialSitesWithoutFirstPost) && $socialSitesWithoutFirstPost->count() > 0 && \App\Models\Setting::get('first_post_required_global', true))
    <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">{{ __('client.first_post_reminder_title') }}</p>
                <p class="text-xs text-yellow-700 dark:text-yellow-400 mb-3">
                    @if($socialSitesWithoutFirstPost->where('platform_type', '!=', 'website')->count() > 0 && $socialSitesWithoutFirstPost->where('platform_type', 'website')->count() > 0)
                        {{ __('client.first_post_reminder_desc_mixed') }}
                    @elseif($socialSitesWithoutFirstPost->where('platform_type', '!=', 'website')->count() > 0)
                        {{ __('client.first_post_reminder_desc_social') }}
                    @else
                        {{ __('client.first_post_reminder_desc_site') }}
                    @endif
                </p>
                <div class="flex flex-col gap-2">
                    @foreach($socialSitesWithoutFirstPost as $s)
                    <div class="flex items-center justify-between gap-3 bg-white dark:bg-gray-900 rounded-lg px-3 py-2 border border-yellow-100 dark:border-yellow-900">
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                            @if($s->platform_type === 'website')
                                {{ $s->domain }}
                            @else
                                {{ ucfirst($s->platform_type) }}: {{ $s->platform_url ?? $s->domain }}
                            @endif
                        </span>
                        @if($s->first_post_url)
                            <span class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('client.first_post_pending_review') }}
                            </span>
                        @else
                        <form method="POST" action="{{ route('webmaster.sites.first-post.submit', $s) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="url" name="first_post_url" required
                                placeholder="{{ $s->platform_type === 'website' ? __('client.first_post_url_placeholder_site') : __('client.first_post_url_placeholder_social') }}"
                                class="text-xs border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 w-56 focus:outline-none focus:ring-1 focus:ring-yellow-400">
                            <button type="submit" class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg font-medium transition whitespace-nowrap">
                                {{ __('client.first_post_confirm') }}
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_total_sites') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sitesCount }}</p>
            <a href="{{ route('webmaster.sites.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">{{ __('client.wm_view_all_sites') }}</a>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.active_links') }}</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $activeLinks }}</p>
            @if($pendingLinks > 0)
                <p class="text-xs text-yellow-500 mt-1">{{ $pendingLinks }} {{ __('client.status_pending') }}</p>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.balance') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($balance, 2) }}</p>
            @if($frozen > 0)
            <p class="text-xs text-yellow-500 dark:text-yellow-400 mt-1">{{ __('wallet.frozen_balance') }}: ${{ number_format($frozen, 2) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400">{{ __('wallet.available_for_withdrawal') }}: ${{ number_format($availableForWithdrawal, 2) }}</p>
            @endif
            <a href="{{ route('webmaster.wallet') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">{{ __('client.wallet') }}</a>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_earned_30') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalEarned30, 2) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Earnings line chart --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_earnings_chart') }}</h2>
            @if(array_sum($earningsDays['values']) > 0)
                <div class="relative h-48">
                    <canvas id="earningsChart"></canvas>
                </div>
            @else
                <div class="h-48 flex flex-col items-center justify-center text-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                    <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">{{ __('client.dash_no_data_hint') }}</p>
                </div>
            @endif
        </div>

        {{-- Links donut --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_links_by_status') }}</h2>
            @php $hasLinks = array_sum(array_values($linkStatuses)) > 0; @endphp
            @if($hasLinks)
                <div class="relative h-48">
                    <canvas id="linksDonut"></canvas>
                </div>
            @else
                <div class="h-48 flex flex-col items-center justify-center text-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('client.recent_orders') }}</h2>
            <a href="{{ route('webmaster.orders.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('client.view_all') }}</a>
        </div>
        @if($recentLinks->isEmpty())
            <div class="px-6 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">{{ __('client.no_orders_yet') }}</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase">
                <tr>
                    <th class="text-left px-6 py-3">{{ __('client.col_domain') }}</th>
                    <th class="text-left px-6 py-3 hidden sm:table-cell">{{ __('client.col_type') }}</th>
                    <th class="text-left px-6 py-3 hidden md:table-cell">{{ __('client.col_price') }}</th>
                    <th class="text-left px-6 py-3">{{ __('client.col_status') }}</th>
                    <th class="text-left px-6 py-3 hidden sm:table-cell">{{ __('client.col_date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($recentLinks as $link)
                @php
                    $sc = match($link->status) {
                        'active'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                        'pending'  => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                        'paused'   => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                        default    => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                    };
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-6 py-3 font-medium text-gray-900 dark:text-white text-xs truncate max-w-xs">{{ $link->donor_url }}</td>
                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 hidden sm:table-cell text-xs">{{ $link->placement_type }}</td>
                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell text-xs">${{ $link->price_per_day }}/{{ __('client.day') }}</td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $sc }}">{{ __('client.status_'.$link->status) }}</span>
                        @if($link->status === 'paused' && $link->pause_reason)
                            <div class="text-xs text-yellow-600 dark:text-yellow-500 mt-1">{{ __('client.pause_reason_' . $link->pause_reason) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-400 dark:text-gray-500 text-xs hidden sm:table-cell">{{ $link->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
(function () {
    const isDark     = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#9ca3af' : '#6b7280';
    const tooltipBg  = isDark ? '#1f2937' : '#ffffff';
    const tooltipFg  = isDark ? '#f9fafb' : '#111827';

    Chart.defaults.font.family = 'inherit';
    Chart.defaults.color = textColor;

    @if(array_sum($earningsDays['values']) > 0)
    new Chart(document.getElementById('earningsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($earningsDays['labels']) !!},
            datasets: [{
                data: {!! json_encode($earningsDays['values']) !!},
                borderColor: '#22c55e',
                backgroundColor: isDark ? 'rgba(34,197,94,0.15)' : 'rgba(34,197,94,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#22c55e',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: tooltipFg,
                    bodyColor: tooltipFg,
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    callbacks: { label: ctx => ' $' + ctx.parsed.y.toFixed(2) }
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { maxTicksLimit: 10 } },
                y: { grid: { color: gridColor }, beginAtZero: true, ticks: { callback: v => '$' + v } }
            }
        }
    });
    @endif

    @if(array_sum(array_values($linkStatuses)) > 0)
    new Chart(document.getElementById('linksDonut'), {
        type: 'doughnut',
        data: {
            labels: ['{{ __("client.status_active") }}','{{ __("client.status_paused") }}','{{ __("client.status_pending") }}','{{ __("client.status_expired") }}'],
            datasets: [{
                data: [{{ $linkStatuses['active'] }},{{ $linkStatuses['paused'] }},{{ $linkStatuses['pending'] }},{{ $linkStatuses['expired'] }}],
                backgroundColor: ['#22c55e','#eab308','#94a3b8','#ef4444'],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: tooltipFg,
                    bodyColor: tooltipFg,
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                }
            }
        }
    });
    @endif
})();
</script>
@endpush
