@extends('client.layouts.app')
@section('title', 'Токени')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Токени сніпетів</h1>
    <a href="{{ route('client.tokens.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">+ Створити токен</a>
</div>

<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    @if($tokens->isEmpty())
        <div class="p-8 text-center text-gray-400 dark:text-gray-500">
            <p>Токенів ще немає.</p>
            <a href="{{ route('client.tokens.create') }}" class="mt-2 inline-block text-blue-600 hover:underline">Створити перший токен</a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">{{ __('client.wm_sites_title') }}</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Токен</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Ліміт</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Тип</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Використано</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Статус</th>
                    <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Дії</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($tokens as $token)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $token->site->domain ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ substr($token->token, 0, 20) }}...</code>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $token->link_limit }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $token->link_type }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $token->last_used_at?->diffForHumans() ?? 'Ніколи' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $token->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $token->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('client.tokens.show', $token) }}" class="text-blue-600 hover:underline text-sm">Код</a>
                        @if($token->status === 'active')
                            <form method="POST" action="{{ route('client.tokens.revoke', $token) }}">
                                @csrf
                                <button class="text-red-500 hover:text-red-700 text-sm">Відкликати</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('client.tokens.activate', $token) }}">
                                @csrf
                                <button class="text-green-600 hover:text-green-800 text-sm">Активувати</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $tokens->links() }}</div>
    @endif
</div>
@endsection
