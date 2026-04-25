@extends('webmaster.layouts.app')
@section('title', __('client.wm_orders_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.wm_orders_title') }}</h1>
    </div>

    {{-- Фільтри --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(['' => __('client.filter_all'), 'pending' => __('client.filter_pending'), 'active' => __('client.filter_active'), 'paused' => __('client.filter_paused'), 'rejected' => __('client.filter_rejected')] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="text-xs px-3 py-1.5 rounded-lg border {{ request('status', '') === $val ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    @if($links->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center text-gray-400 text-sm">
            {{ __('client.no_orders') }}
        </div>
    @else

    @php
    function wm_status_classes($status) {
        return match($status) {
            'active'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'paused'   => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'pending'  => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
            default    => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
        };
    }
    @endphp

    {{-- Mobile: картки --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($links as $link)
        @php
            $sc = wm_status_classes($link->status);
            $sl = match($link->status) {
                'active'   => __('client.status_active'),
                'paused'   => __('client.status_paused'),
                'pending'  => __('client.status_pending'),
                'approved' => __('client.status_approved'),
                'rejected' => __('client.status_rejected'),
                'expired'  => __('client.status_expired'),
                default    => $link->status,
            };
        @endphp
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-2 mb-3">
                <div class="flex-1 min-w-0">
                    @if($link->donor_url)
                    <a href="{{ $link->donor_url }}?t={{ time() }}" target="_blank"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all leading-tight">
                        {{ parse_url($link->donor_url, PHP_URL_HOST) }}{{ Str::limit(parse_url($link->donor_url, PHP_URL_PATH), 40) }}
                    </a>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-0.5">
                    <span class="flex-shrink-0 text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
                    @if($link->status === 'paused' && $link->pause_reason)
                        <span class="text-xs text-yellow-600 dark:text-yellow-500">
                            {{ __('client.pause_reason_' . $link->pause_reason) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Ціль --}}
            <div class="mb-3">
                <a href="{{ $link->target_url }}" target="_blank"
                   class="text-xs text-blue-500 dark:text-blue-400 hover:underline block truncate">{{ $link->target_url }}</a>
                @if($link->anchor)
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $link->anchor }}</span>
                @endif
            </div>

            {{-- Тип розміщення --}}
            <div class="mb-3">
                @if($link->placement_type === 'onclick')
                    <span class="inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs px-2 py-0.5 rounded font-medium">JS Onclick</span>
                @elseif(str_contains($link->placement_type, 'article'))
                    <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs px-2 py-0.5 rounded font-medium">{{ __('client.col_placement_article') }}</span>
                @else
                    <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded font-medium">{{ __('client.col_placement_link') }}</span>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-700 dark:text-gray-200">${{ number_format($link->price_per_day, 2) }}/{{ __('client.per_day') }}</span>

                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('webmaster.orders.edit', $link) }}"
                       class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                        {{ __('client.edit') }}
                    </a>
                    @if($link->status === 'pending')
                    <form method="POST" action="{{ route('webmaster.orders.approve', $link) }}">
                        @csrf
                        <button class="text-xs border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 px-2.5 py-1 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20">
                            {{ __('client.approve') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('webmaster.orders.reject', $link) }}"
                          onsubmit="return confirm('{{ __('client.reject_confirm') }}')">
                        @csrf
                        <button class="text-xs border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            {{ __('client.reject') }}
                        </button>
                    </form>
                    @endif
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
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_donor') }}</th>
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_target') }}</th>
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_placement') }}</th>
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_price') }}</th>
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_status') }}</th>
                    <th class="text-left px-4 py-3 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase">{{ __('client.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($links as $link)
                @php
                    $sc = wm_status_classes($link->status);
                    $sl = match($link->status) {
                        'active'   => __('client.status_active'),
                        'paused'   => __('client.status_paused'),
                        'pending'  => __('client.status_pending'),
                        'approved' => __('client.status_approved'),
                        'rejected' => __('client.status_rejected'),
                        'expired'  => __('client.status_expired'),
                        default    => $link->status,
                    };
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 max-w-xs">
                        @if($link->donor_url)
                        <a href="{{ $link->donor_url }}?t={{ time() }}" target="_blank"
                           class="hover:underline text-blue-600 dark:text-blue-400 break-all">
                            {{ parse_url($link->donor_url, PHP_URL_HOST) }}{{ Str::limit(parse_url($link->donor_url, PHP_URL_PATH), 40) }}
                        </a>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        <a href="{{ $link->target_url }}" target="_blank"
                           class="text-blue-500 dark:text-blue-400 hover:underline block truncate max-w-xs">{{ $link->target_url }}</a>
                        <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $link->anchor ?: '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($link->placement_type === 'onclick')
                            <span class="inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs px-2 py-0.5 rounded font-medium mb-1">JS Onclick</span>
                            @if($link->onclick_href)
                                <div class="text-gray-500 dark:text-gray-400">{{ $link->onclick_href }}</div>
                            @else
                                <div class="text-red-400">{{ __('client.no_href') }}</div>
                            @endif
                        @elseif(str_contains($link->placement_type, 'article'))
                            <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs px-2 py-0.5 rounded font-medium">{{ __('client.col_placement_article') }}</span>
                        @else
                            <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded font-medium mb-1">{{ __('client.col_placement_link') }}</span>
                            @if($link->anchor_before || $link->anchor_after)
                                <div class="text-gray-400 dark:text-gray-500">
                                    {{ $link->anchor_before }}
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('client.anchor_placeholder') }}</span>
                                    {{ $link->anchor_after }}
                                </div>
                            @else
                                <div class="text-red-400">{{ __('client.text_not_filled') }}</div>
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-700 dark:text-gray-200">
                        ${{ number_format($link->price_per_day, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
                        @if($link->status === 'paused' && $link->pause_reason)
                            <div class="text-xs text-yellow-600 dark:text-yellow-500 mt-1">
                                {{ __('client.pause_reason_' . $link->pause_reason) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('webmaster.orders.edit', $link) }}"
                               class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                                {{ __('client.edit') }}
                            </a>
                            @if($link->status === 'pending')
                            <form method="POST" action="{{ route('webmaster.orders.approve', $link) }}">
                                @csrf
                                <button class="text-xs border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 px-2.5 py-1 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20">
                                    {{ __('client.approve') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('webmaster.orders.reject', $link) }}"
                                  onsubmit="return confirm('{{ __('client.reject_confirm') }}')">
                                @csrf
                                <button class="text-xs border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                    {{ __('client.reject') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">{{ $links->links() }}</div>
    </div>
    <div class="mt-4 md:hidden">{{ $links->links() }}</div>

    @endif
</div>
@endsection
