@extends('client.layouts.app')
@section('title', __('direct_payments.title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('direct_payments.title') }}</h1>
        <a href="{{ route('client.direct-payments.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-black text-sm font-medium rounded-lg transition">
            {{ __('direct_payments.new') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Balances --}}
    @if($balances->isNotEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('direct_payments.balances_title') }}</h2>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($balances as $webmasterId => $balance)
                    @php $wm = $webmasters[$webmasterId] ?? null @endphp
                    @if($wm)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $wm->name }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($balance, 2) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Payments list --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($payments as $payment)
                <div class="p-4 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->webmaster->name }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($payment->status === 'confirmed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                            @elseif($payment->status === 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                            @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                            {{ __('direct_payments.status_' . $payment->status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $payment->created_at->format('d.m.Y') }}</span>
                    </div>
                    @if($payment->note)
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->note }}</p>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('direct_payments.no_payments') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <table class="hidden md:table w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.webmaster') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.amount') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.status') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.note') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $payment->webmaster->name }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($payment->status === 'confirmed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($payment->status === 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                {{ __('direct_payments.status_' . $payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $payment->note ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('direct_payments.no_payments') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
