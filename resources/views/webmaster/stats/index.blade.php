@extends('webmaster.layouts.app')
@section('title', __('nav.stats'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="flex flex-wrap gap-3 justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('nav.stats') }}</h1>
        <div class="flex gap-2">
            @foreach([7, 14, 30, 90] as $d)
            <a href="{{ route('webmaster.stats', ['days' => $d]) }}"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                    {{ $days == $d
                        ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                        : 'bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                {{ $d }}{{ __('client.days_short') }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Метрики --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_views') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalViews) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('client.stats_for_days', ['days' => $days]) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_clicks') }}</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalClicks) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('client.stats_for_days', ['days' => $days]) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">CTR</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $avgCtr }}%</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('client.stats_for_days', ['days' => $days]) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_earned_30') }}</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalEarnings, 2) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('client.stats_for_days', ['days' => $days]) }}</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Impressions / Clicks --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_chart_title') }}</h2>
            @if(array_sum($chartImpressions) == 0 && array_sum($chartClicks) == 0)
                <div class="h-48 flex flex-col items-center justify-center text-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                </div>
            @else
                <div class="relative h-48">
                    <canvas id="statsChart"></canvas>
                </div>
            @endif
        </div>

        {{-- Earnings --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_earnings_chart') }}</h2>
            @if(array_sum($chartEarnings) == 0)
                <div class="h-48 flex flex-col items-center justify-center text-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                </div>
            @else
                <div class="relative h-48">
                    <canvas id="earningsChart"></canvas>
                </div>
            @endif
        </div>

    </div>

    {{-- По сайтах --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_by_sites') }}</h2>
        @if($siteStats->isEmpty())
            <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_data') }}</p>
        @else

        {{-- Bar chart по сайтах --}}
        @if($siteStats->count() > 1)
        <div class="relative h-40 mb-5">
            <canvas id="siteBarChart"></canvas>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="text-left py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('client.col_domain') }}</th>
                        <th class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('client.stats_views') }}</th>
                        <th class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('client.stats_clicks') }}</th>
                        <th class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 font-medium hidden sm:table-cell">{{ __('client.stats_unique') }}</th>
                        <th class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">CTR</th>
                        <th class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 font-medium hidden sm:table-cell">{{ __('client.stats_links_count') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($siteStats as $stat)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2.5 font-medium text-gray-900 dark:text-white text-xs">{{ $stat->domain }}</td>
                        <td class="py-2.5 text-center text-gray-600 dark:text-gray-300 text-xs">{{ number_format($stat->views) }}</td>
                        <td class="py-2.5 text-center text-blue-600 dark:text-blue-400 font-medium text-xs">{{ number_format($stat->clicks) }}</td>
                        <td class="py-2.5 text-center text-gray-500 dark:text-gray-400 text-xs hidden sm:table-cell">{{ number_format($stat->unique_visitors) }}</td>
                        <td class="py-2.5 text-center text-indigo-600 dark:text-indigo-400 text-xs">
                            {{ $stat->views > 0 ? round($stat->clicks / $stat->views * 100, 1) : 0 }}%
                        </td>
                        <td class="py-2.5 text-center text-gray-500 dark:text-gray-400 text-xs hidden sm:table-cell">{{ $stat->links_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Топ анкорів --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('client.stats_top_anchors') }}</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('client.stats_anchors_hint') }}</span>
        </div>
        @if($topAnchors->isEmpty())
            <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_anchors') }}</p>
        @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($topAnchors as $anchor)
            <div class="py-3 flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anchor->anchor_text ?: '—' }}</p>
                    <a href="{{ $anchor->anchor_href }}" target="_blank"
                       class="text-xs text-blue-500 dark:text-blue-400 hover:underline truncate block">{{ $anchor->anchor_href }}</a>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $anchor->domain }}</p>
                </div>
                <span class="flex-shrink-0 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-medium">
                    {{ $anchor->total_clicks }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
(function () {
    const isDark    = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const tooltipBg = isDark ? '#1f2937' : '#ffffff';
    const tooltipFg = isDark ? '#f9fafb' : '#111827';

    Chart.defaults.font.family = 'inherit';
    Chart.defaults.color = textColor;

    const tooltipDefaults = {
        backgroundColor: tooltipBg,
        titleColor: tooltipFg,
        bodyColor: tooltipFg,
        borderColor: isDark ? '#374151' : '#e5e7eb',
        borderWidth: 1,
    };

    @if(array_sum($chartImpressions) > 0 || array_sum($chartClicks) > 0)
    new Chart(document.getElementById('statsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: '{{ __("client.stats_impressions") }}',
                    data: {!! json_encode($chartImpressions) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: isDark ? 'rgba(59,130,246,0.12)' : 'rgba(59,130,246,0.07)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: '{{ __("client.stats_clicks") }}',
                    data: {!! json_encode($chartClicks) !!},
                    borderColor: '#10b981',
                    backgroundColor: isDark ? 'rgba(16,185,129,0.12)' : 'rgba(16,185,129,0.07)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    fill: true,
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: textColor, boxWidth: 10, padding: 12, font: { size: 11 } } },
                tooltip: tooltipDefaults,
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { maxTicksLimit: 10 } },
                y: { grid: { color: gridColor }, beginAtZero: true }
            }
        }
    });
    @endif

    @if(array_sum($chartEarnings) > 0)
    new Chart(document.getElementById('earningsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                data: {!! json_encode($chartEarnings) !!},
                borderColor: '#22c55e',
                backgroundColor: isDark ? 'rgba(34,197,94,0.15)' : 'rgba(34,197,94,0.08)',
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
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
                tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ' $' + ctx.parsed.y.toFixed(2) } },
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { maxTicksLimit: 10 } },
                y: { grid: { color: gridColor }, beginAtZero: true, ticks: { callback: v => '$' + v } }
            }
        }
    });
    @endif

    @if($siteStats->count() > 1)
    new Chart(document.getElementById('siteBarChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($siteStats->pluck('domain')) !!},
            datasets: [
                {
                    label: '{{ __("client.stats_views") }}',
                    data: {!! json_encode($siteStats->pluck('views')) !!},
                    backgroundColor: isDark ? 'rgba(59,130,246,0.6)' : 'rgba(59,130,246,0.5)',
                    borderRadius: 4,
                },
                {
                    label: '{{ __("client.stats_clicks") }}',
                    data: {!! json_encode($siteStats->pluck('clicks')) !!},
                    backgroundColor: isDark ? 'rgba(16,185,129,0.6)' : 'rgba(16,185,129,0.5)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: textColor, boxWidth: 10, padding: 12, font: { size: 11 } } },
                tooltip: tooltipDefaults,
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { maxTicksLimit: 10 } },
                y: { grid: { color: gridColor }, beginAtZero: true }
            }
        }
    });
    @endif
})();
</script>
@endpush
