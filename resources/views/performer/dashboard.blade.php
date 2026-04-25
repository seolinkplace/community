@extends('performer.layouts.app')
@section('title', __('nav.dashboard'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('nav.dashboard') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ __('nav.greeting', ['name' => $user->name]) }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.balance') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($balance, 2) }}</p>
            @if(($wallet?->pending ?? 0) > 0)
                <p class="text-xs text-gray-400 mt-1">{{ __('client.wm_pending') }} ${{ number_format($wallet->pending, 2) }}</p>
            @endif
            <a href="{{ route('performer.wallet') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">{{ __('nav.wallet') }}</a>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_earned_30') }}</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalEarned30, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_completed_tasks') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedTasks }}</p>
            @if($pendingTasks > 0)
                <p class="text-xs text-yellow-500 mt-1">{{ $pendingTasks }} {{ __('client.dash_pending_tasks') }}</p>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.dash_available_tasks') }}</p>
            <p class="text-2xl font-bold {{ $availableTasks > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-900 dark:text-white' }}">{{ $availableTasks }}</p>
            @if($availableTasks > 0)
                <a href="{{ route('performer.tasks.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1 block">{{ __('nav.view_tasks') }}</a>
            @endif
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

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

        {{-- Tasks donut --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.dash_tasks_by_status') }}</h2>
            @php $hasTaskData = array_sum(array_values($taskStatuses)) > 0; @endphp
            @if($hasTaskData)
                <div class="relative h-48">
                    <canvas id="tasksDonut"></canvas>
                </div>
            @else
                <div class="h-48 flex flex-col items-center justify-center text-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('client.dash_no_data') }}</p>
                </div>
            @endif
        </div>

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

    @if(array_sum($earningsDays['values']) > 0)
    new Chart(document.getElementById('earningsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($earningsDays['labels']) !!},
            datasets: [{
                data: {!! json_encode($earningsDays['values']) !!},
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
                y: { grid: { color: gridColor }, beginAtZero: true, ticks: { callback: v => '$' + v } }
            }
        }
    });
    @endif

    @if(array_sum(array_values($taskStatuses)) > 0)
    new Chart(document.getElementById('tasksDonut'), {
        type: 'doughnut',
        data: {
            labels: ['{{ __("client.status_approved") }}','{{ __("client.status_pending") }}','{{ __("client.status_rejected") }}'],
            datasets: [{
                data: [{{ $taskStatuses['approved'] }},{{ $taskStatuses['pending'] }},{{ $taskStatuses['rejected'] }}],
                backgroundColor: ['#22c55e','#eab308','#ef4444'],
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
