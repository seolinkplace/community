@extends('client.layouts.app')
@section('title', __('client.dashboard_title'))

@section('content')
@php
    $client = \App\Helpers\AuthHelper::client();
    $hasLinks = ($statsActive + $statsPaused + $statsPending + $statsExpired) > 0;
    $hasSpending = array_sum($spendingDays['values']) > 0;
@endphp

{{-- Stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.balance') }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($balance, 2) }}</p>
        <a href="{{ route('client.wallet') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">{{ __('client.wallet') }}</a>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_active_links') }}</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statsActive }}</p>
        @if($statsPending > 0)
            <p class="text-xs text-yellow-500 mt-1">+{{ $statsPending }} {{ __('client.dash_pending_links') }}</p>
        @endif
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_paused_links') }}</p>
        <p class="text-2xl font-bold {{ $statsPaused > 0 ? 'text-yellow-500' : 'text-gray-900 dark:text-white' }}">{{ $statsPaused }}</p>
        @if($statsPaused > 0)
            <a href="{{ route('client.orders.index', ['status' => 'paused']) }}" class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline mt-1 block">{{ __('client.view_all') }}</a>
        @endif
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_spent_30') }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalSpent30, 2) }}</p>
    </div>
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Spending line chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_spending_chart') }}</h2>
        @if($hasSpending)
            <div class="relative h-48">
                <canvas id="spendingChart"></canvas>
            </div>
        @else
            <div class="h-48 flex flex-col items-center justify-center text-center">
                <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">{{ __('client.dash_no_data_hint') }}</p>
            </div>
        @endif
    </div>

    {{-- Links donut chart --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_links_by_status') }}</h2>
        @if($hasLinks)
            <div class="relative h-48">
                <canvas id="linksDonut"></canvas>
            </div>
        @else
            <div class="h-48 flex flex-col items-center justify-center text-center">
                <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
            </div>
        @endif
    </div>

</div>

{{-- Two columns: recent links + recent transactions --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- Recent links --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('client.recent_links') }}</h2>
            <a href="{{ route('client.orders.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('client.all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($recentLinks as $link)
            <div class="px-5 py-3 flex items-center justify-between gap-4">
                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $link->anchor ?? $link->target_url }}</span>
                <div class="flex flex-col items-end gap-0.5 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $link->status === 'active'  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                          ($link->status === 'paused'  ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' :
                          ($link->status === 'pending' ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' :
                           'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400')) }}">
                        {{ match($link->status) {
                            'active'   => __('client.status_active'),
                            'paused'   => __('client.status_paused'),
                            'pending'  => __('client.status_pending'),
                            'approved' => __('client.status_approved'),
                            'rejected' => __('client.status_rejected'),
                            'expired'  => __('client.status_expired'),
                            default    => $link->status,
                        } }}
                    </span>
                    @if($link->status === 'paused' && $link->pause_reason)
                        <span class="text-xs text-yellow-600 dark:text-yellow-500">{{ __('client.pause_reason_' . $link->pause_reason) }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">{{ __('client.no_links') }}</div>
            @endforelse
        </div>
    </div>

    {{-- Recent transactions --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('client.recent_tx') }}</h2>
            <a href="{{ route('client.wallet') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('client.all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @php $txList = $client->wallet?->transactions()->latest()->limit(5)->get() ?? collect(); @endphp
            @forelse($txList as $tx)
            <div class="px-5 py-3 flex items-center justify-between gap-4 overflow-hidden">
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                        @php
                            $desc    = $tx->description ?? '';
                            $decoded = json_decode($desc, true);
                            $label   = is_array($decoded) ? ucfirst($tx->type) : ($desc ?: ucfirst($tx->type));
                        @endphp
                        {{ $label }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $tx->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <span class="text-sm font-medium flex-shrink-0 {{ in_array($tx->type, ['deposit','refund']) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ in_array($tx->type, ['deposit','refund']) ? '+' : '-' }}${{ number_format(abs($tx->amount), 2) }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">{{ __('client.no_tx') }}</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#9ca3af' : '#6b7280';
    const tooltipBg  = isDark ? '#1f2937' : '#ffffff';
    const tooltipFg  = isDark ? '#f9fafb' : '#111827';

    Chart.defaults.font.family = 'inherit';
    Chart.defaults.color = textColor;

    @if($hasSpending)
    new Chart(document.getElementById('spendingChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($spendingDays['labels']) !!},
            datasets: [{
                data: {!! json_encode($spendingDays['values']) !!},
                borderColor: '#6366f1',
                backgroundColor: isDark ? 'rgba(99,102,241,0.15)' : 'rgba(99,102,241,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#6366f1',
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
                y: {
                    grid: { color: gridColor },
                    beginAtZero: true,
                    ticks: { callback: v => '$' + v }
                }
            }
        }
    });
    @endif

    @if($hasLinks)
    const donutLabels = [
        '{{ __("client.status_active") }}',
        '{{ __("client.status_paused") }}',
        '{{ __("client.status_pending") }}',
        '{{ __("client.status_expired") }}',
    ];
    new Chart(document.getElementById('linksDonut'), {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: [{{ $linkStatuses['active'] }}, {{ $linkStatuses['paused'] }}, {{ $linkStatuses['pending'] }}, {{ $linkStatuses['expired'] }}],
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
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                },
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
