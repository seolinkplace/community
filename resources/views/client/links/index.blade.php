@extends('client.layouts.app')
@section('title', 'Посилання')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Посилання</h1>
    <a href="{{ route('client.links.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">+ Додати</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 mb-4 p-4">
    <form method="GET" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('client.search_placeholder') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">Всі статуси</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="not_found" {{ request('status') === 'not_found' ? 'selected' : '' }}>Not found</option>
            <option value="removed" {{ request('status') === 'removed' ? 'selected' : '' }}>Removed</option>
            <option value="pending_check" {{ request('status') === 'pending_check' ? 'selected' : '' }}>Pending</option>
        </select>
        <select name="project" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">Всі проекти</option>
            @foreach($projects as $project)
                <option value="{{ $project }}" {{ request('project') === $project ? 'selected' : '' }}>{{ $project }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">{{ __('client.filter_btn') }}</button>
        <a href="{{ route('client.links.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">Скинути</a>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    @if($links->isEmpty())
        <div class="p-8 text-center text-gray-400">
            <p>{{ __('client.no_links_yet') }}</p>
            <a href="{{ route('client.links.create') }}" class="mt-2 inline-block text-blue-600 hover:underline">Додати перше посилання</a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Донор URL</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Анкор</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Тип</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Проект</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Статус</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Дії</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($links as $link)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ $link->donor_url }}" target="_blank" class="text-blue-600 hover:underline truncate block max-w-6xl">
                            {{ parse_url($link->donor_url, PHP_URL_HOST) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $link->anchor ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded text-xs {{ $link->link_type === 'dofollow' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $link->link_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $link->project ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs
                            {{ $link->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($link->status === 'not_found' ? 'bg-red-100 text-red-700' :
                               ($link->status === 'removed' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')) }}">
                            {{ $link->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('client.links.edit', $link) }}" class="text-gray-600 hover:text-gray-900">Ред.</a>
                        <form method="POST" action="{{ route('client.links.destroy', $link) }}" onsubmit="return confirm('Видалити?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Вид.</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">{{ $links->links() }}</div>
    @endif
</div>
@endsection
