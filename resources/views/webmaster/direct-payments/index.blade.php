@extends('webmaster.layouts.app')
@section('title', __('direct_payments.title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ __('direct_payments.title') }}</h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($payments as $payment)
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->client->name }}</span>
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
                    @if($payment->isPending())
                        <div class="flex gap-2 pt-1">
                            <form method="POST" action="{{ route('webmaster.direct-payments.confirm', $payment->uuid) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition">
                                    {{ __('direct_payments.confirm') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('webmaster.direct-payments.reject', $payment->uuid) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition">
                                    {{ __('direct_payments.reject') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('direct_payments.no_pending') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <table class="hidden md:table w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.client') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.amount') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.status') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.note') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('direct_payments.date') }}</th>
                    <th class="px-4 py-3 text-left"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $payment->client->name }}</td>
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
                        <td class="px-4 py-3">
                            @if($payment->isPending())
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('webmaster.direct-payments.confirm', $payment->uuid) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition">
                                            {{ __('direct_payments.confirm') }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('webmaster.direct-payments.reject', $payment->uuid) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition">
                                            {{ __('direct_payments.reject') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('direct_payments.no_pending') }}
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
