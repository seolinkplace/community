@extends('client.layouts.app')
@section('title', $campaign->name)

@section('content')
<div class="w-full max-w-6xl mx-auto py-8 px-4">

    {{-- Хедер --}}
    <div class="flex items-center gap-3 mb-6 flex-wrap">
        <a href="{{ route('client.campaigns.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Кампанії</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-bold text-gray-900">{{ $campaign->name }}</h1>
        @php
            $badgeColor = match($campaign->status) {
                'active'    => 'bg-green-100 text-green-700',
                'paused'    => 'bg-yellow-100 text-yellow-700',
                'draft'     => 'bg-gray-100 text-gray-500',
                'completed' => 'bg-blue-100 text-blue-700',
                default     => 'bg-gray-100 text-gray-500',
            };
            $badgeLabel = match($campaign->status) {
                'active'    => __('client.status_active_f'),
                'paused'    => __('client.status_paused_f'),
                'draft'     => __('client.status_draft'),
                'completed' => __('client.status_completed'),
                default     => $campaign->status,
            };
        @endphp
        <span class="text-xs px-2 py-1 rounded-full {{ $badgeColor }}">{{ $badgeLabel }}</span>

        <div class="ml-auto flex gap-2">
            @if($campaign->status === 'active')
            <form method="POST" action="{{ route('client.campaigns.pause', $campaign) }}">
                @csrf
                <button class="text-xs border border-yellow-300 text-yellow-700 px-3 py-1.5 rounded-lg hover:bg-yellow-50">
                    Призупинити кампанію
                </button>
            </form>
            @elseif($campaign->status === 'paused')
            <form method="POST" action="{{ route('client.campaigns.resume', $campaign) }}">
                @csrf
                <button class="text-xs border border-green-300 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-50">
                    Відновити кампанію
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    {{-- Статистика --}}
    @php
        $totalDay    = $links->sum('price_per_day');
        $activeCount = $links->where('status', 'active')->count();
        $pausedCount = $links->where('status', 'paused')->count();
    @endphp
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $links->total() }}</div>
            <div class="text-xs text-gray-400 mt-1">Всього посилань</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $activeCount }}</div>
            <div class="text-xs text-gray-400 mt-1">Активних</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">${{ number_format($totalDay, 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">Витрати / день</div>
        </div>
    </div>

    {{-- Список посилань --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Посилання ({{ $links->total() }})</h2>
        </div>

        @forelse($links as $link)
        @php
            $sc = match($link->status) {
                'active'   => 'bg-green-100 text-green-700',
                'paused'   => 'bg-yellow-100 text-yellow-700',
                'pending'  => 'bg-gray-100 text-gray-500',
                'rejected' => 'bg-red-100 text-red-600',
                'expired'  => 'bg-red-100 text-red-600',
                default    => 'bg-gray-100 text-gray-500',
            };
            $sl = match($link->status) {
                'active'   => __('client.status_active'),
                'paused'   => __('client.status_paused'),
                'pending'  => __('client.status_pending_approval'),
                'approved' => __('client.status_approved'),
                'rejected' => __('client.status_rejected'),
                'expired'  => __('client.status_balance_depleted'),
                default    => $link->status,
            };
            $typeLabel = match($link->placement_type) {
                'link'          => 'Посилання',
                'onclick'       => 'Onclick',
                'article_once'  => __('client.type_article_once'),
                'article_daily' => __('client.type_article_daily'),
                default         => $link->placement_type,
            };
        @endphp
        <div class="px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-medium text-gray-800">{{ $link->anchor ?: '(без анкора)' }}</span>
                        <span class="text-gray-300">—</span>
                        <a href="{{ $link->target_url }}" target="_blank"
                           class="text-xs text-blue-500 hover:underline truncate max-w-sm">
                            {{ $link->target_url }}
                        </a>
                    </div>

                    <div class="flex items-center gap-4 mt-1.5 text-xs text-gray-400 flex-wrap">
                        @if($link->donor_url)
                        <span>{{ __('client.placed_on') }}
                            <a href="{{ $link->donor_url }}" target="_blank" class="text-gray-500 hover:underline">
                                {{ $link->donor_url }}
                            </a>
                        </span>
                        @endif
                        <span class="border border-gray-200 rounded px-1.5 py-0.5 text-gray-500">{{ $typeLabel }}</span>
                        <span class="font-medium text-gray-600">${{ number_format($link->price_per_day, 2) }}/день</span>
                        @if($link->started_at)
                            <span>з {{ \Carbon\Carbon::parse($link->started_at)->format('d.m.Y') }}</span>
                        @endif
                    </div>

                    @if($link->status === 'paused' && str_contains($link->notes ?? '', 'баланс'))
                    <div class="mt-1.5 text-xs text-yellow-700 bg-yellow-50 border border-yellow-200 rounded px-2 py-1 inline-block">
                        {{ __('client.paused_auto') }}
                    </div>
                    @elseif($link->status === 'rejected' && $link->notes)
                    <div class="mt-1.5 text-xs text-red-700 bg-red-50 border border-red-200 rounded px-2 py-1 inline-block">
                        {{ $link->notes }}
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>

                    @if($link->status === 'active')
                    <form method="POST" action="{{ route('client.campaigns.links.pause', $link) }}">
                        @csrf
                        <button class="text-xs border border-gray-300 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100">
                            Пауза
                        </button>
                    </form>
                    @elseif($link->status === 'paused')
                    <form method="POST" action="{{ route('client.campaigns.links.resume', $link) }}">
                        @csrf
                        <button class="text-xs border border-green-300 text-green-700 px-2.5 py-1 rounded-lg hover:bg-green-50">
                            Відновити
                        </button>
                    </form>
                    @endif

                    @if($link->donor_url)
                    <a href="{{ route('client.chat.show', $link) }}"
                       class="text-xs border border-gray-200 text-gray-500 px-2.5 py-1 rounded-lg hover:bg-gray-50">
                        Чат
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-400">Посилань в кампанії немає</div>
        @endforelse
    </div>

    @if($links->hasPages())
    <div class="mt-4">{{ $links->links() }}</div>
    @endif

</div>
@endsection
