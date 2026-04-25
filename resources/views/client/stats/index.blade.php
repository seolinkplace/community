@extends('client.layouts.app')
@section('title', __('nav.stats'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="flex flex-wrap gap-3 justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('nav.stats') }}</h1>
        <div class="flex gap-2">
            @foreach([7, 14, 30, 90] as $d)
            <a href="{{ route('client.stats', ['days' => $d]) }}"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                    {{ $days == $d
                        ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                        : 'bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                {{ $d }}{{ __('client.days_short') }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Основні метрики --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_active_links') }}</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $activeLinks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_total_links') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalLinks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_new_links', ['days' => $days]) }}</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $newLinks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.stats_pending') }}</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $byStatus['pending'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Impressions/Clicks chart --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_chart_title') }}</h2>
        @if(array_sum($chartImpressions) == 0 && array_sum($chartClicks) == 0)
            <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_data') }}</p>
        @else
        <div style="position:relative;height:220px">
            <canvas id="statsChart"></canvas>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
        (function(){
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            var gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.07)';
            var textColor = isDark ? '#9ca3af' : '#6b7280';
            new Chart(document.getElementById('statsChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: '{{ __("client.stats_impressions") }}',
                            data: @json($chartImpressions),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.08)',
                            borderWidth: 2,
                            pointRadius: 2,
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: '{{ __("client.stats_clicks") }}',
                            data: @json($chartClicks),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.08)',
                            borderWidth: 2,
                            pointRadius: 2,
                            fill: true,
                            tension: 0.3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: textColor, boxWidth: 12, font: { size: 12 } } } },
                    scales: {
                        x: { ticks: { color: textColor, maxTicksLimit: 10, font: { size: 11 } }, grid: { color: gridColor } },
                        y: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor }, beginAtZero: true }
                    }
                }
            });
        })();
        </script>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        {{-- By status --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_by_status') }}</h2>
            @php
                $statusColors = [
                    'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                    'pending'   => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                    'paused'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                    'rejected'  => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                    'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                    'expired'   => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                ];
            @endphp
            <div class="space-y-2">
                @forelse($byStatus as $status => $count)
                <div class="flex items-center justify-between">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-500' }}">
                        {{ __('client.status_' . $status) }}
                    </span>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $count }}</span>
                </div>
                @empty
                <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_data') }}</p>
                @endforelse
            </div>
        </div>

        {{-- По типах --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_by_type') }}</h2>
            <div class="space-y-2">
                @forelse($byType as $type => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('client.placement_' . $type) }}</span>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $count }}</span>
                </div>
                @empty
                <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_data') }}</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Топ сайтів --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.stats_top_sites') }}</h2>
        @if($topSites->isEmpty())
            <p class="text-gray-400 dark:text-gray-500 text-sm">{{ __('client.stats_no_data') }}</p>
        @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($topSites as $site)
            <div class="flex items-center justify-between py-3">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $site->domain ?? __('client.site_unavailable') }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $site->links_count }} {{ __('client.active_links') }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
