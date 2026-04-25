@extends('client.layouts.app')
@section('title', 'Мої сайти')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">{{ __('client.sites_title') }}</h1>
    <a href="{{ route('client.tokens.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">+ Додати сайт</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    @if($sites->isEmpty())
        <div class="p-8 text-center text-gray-400">
            <p>{{ __('client.no_sites_client') }}</p>
            <a href="{{ route('client.tokens.create') }}" class="mt-2 inline-block text-blue-600 hover:underline">Додати перший сайт</a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Домен</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Тематика</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">DR</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Посилань</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($sites as $site)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $site->domain }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $site->niche ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $site->dr ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $site->links_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">{{ $site->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">{{ $sites->links() }}</div>
    @endif
</div>
@endsection
