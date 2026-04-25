@extends('client.layouts.app')
@section('title', __('client.campaigns_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.campaigns_title') }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    @forelse($campaigns as $campaign)
    @php
        $badgeColor = match($campaign->status) {
            'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'paused'    => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'draft'     => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
            'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            default     => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
        };
        $badgeLabel = match($campaign->status) {
            'active'    => __('client.status_active'),
            'paused'    => __('client.status_paused'),
            'draft'     => __('client.status_draft'),
            'completed' => __('client.status_completed'),
            'cancelled' => __('client.status_cancelled'),
            default     => $campaign->status,
        };
    @endphp
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-3">
        {{-- Назва і бейдж --}}
        <div class="flex items-start justify-between gap-2 mb-2">
            <a href="{{ route('client.campaigns.show', $campaign) }}"
               class="text-base font-semibold text-gray-900 dark:text-white hover:underline leading-tight">
                {{ $campaign->name }}
            </a>
            <span class="flex-shrink-0 text-xs px-2 py-1 rounded-full {{ $badgeColor }}">{{ $badgeLabel }}</span>
        </div>

        @if($campaign->description)
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-2">{{ $campaign->description }}</p>
        @endif

        {{-- Статистика --}}
        <div class="grid grid-cols-3 gap-2 text-xs mb-3">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 text-center">
                <div class="font-bold text-lg text-gray-900 dark:text-white">{{ $campaign->links_count }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ __('client.links_label') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 text-center">
                <div class="font-bold text-lg text-green-600 dark:text-green-400">{{ $campaign->links->where('status', 'active')->count() }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ __('client.active_label') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 text-center">
                <div class="font-bold text-sm text-gray-900 dark:text-white">{{ $campaign->created_at->format('d.m') }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ $campaign->created_at->format('Y') }}</div>
            </div>
        </div>

        {{-- Дії --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('client.campaigns.show', $campaign) }}"
               class="text-xs px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors">
                {{ __('client.details') }}
            </a>
            @if($campaign->status === 'active')
            <form method="POST" action="{{ route('client.campaigns.pause', $campaign) }}"
                  x-data @submit.prevent="if(confirm('{{ __("client.confirm_pause") }}')) $el.submit()">
                @csrf
                <button type="submit" class="text-xs border border-gray-300 dark:border-gray-600 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    {{ __('client.pause') }}
                </button>
            </form>
            @elseif($campaign->status === 'paused')
            <form method="POST" action="{{ route('client.campaigns.resume', $campaign) }}">
                @csrf
                <button class="text-xs border border-green-300 dark:border-green-700 px-3 py-1.5 rounded-lg text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20">
                    {{ __('client.resume') }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
        <p class="text-gray-400 text-sm">{{ __('client.no_campaigns') }}</p>
        <p class="text-gray-400 text-xs mt-1">{{ __('client.no_campaigns_desc') }}</p>
    </div>
    @endforelse

    @if($campaigns->hasPages())
    <div class="mt-4">{{ $campaigns->links() }}</div>
    @endif
</div>
@endsection
