@extends('client.layouts.app')
@section('title', 'Нове замовлення')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Нове замовлення</h1>
    <p class="text-gray-500 text-sm mb-6">Сайт: <strong>{{ $site->domain }}</strong></p>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('client.orders.store') }}">
            @csrf
            <input type="hidden" name="site_id" value="{{ $site->id }}">
            <input type="hidden" name="donor_url" value="{{ request('donor_url', 'https://'.$site->domain) }}">

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Сторінка розміщення</label>
                    <input type="text" value="{{ request('donor_url', 'https://'.$site->domain) }}" disabled
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Цільова сторінка *</label>
                    <input type="url" name="target_url" value="{{ old('target_url') }}" required placeholder="https://yoursite.com/page"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Анкор</label>
                    <input type="text" name="anchor" value="{{ old('anchor', request('anchor')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Тип *</label>
                        <select name="link_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="dofollow">Dofollow</option>
                            <option value="nofollow">Nofollow</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ціна ($)</label>
                        <input type="number" name="price" value="{{ old('price', $site->price) }}" step="0.01" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Проект</label>
                        <input type="text" name="project" value="{{ old('project') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Кампанія</label>
                        <input type="text" name="campaign" value="{{ old('campaign') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Нотатки для вебмастера</label>
                    <textarea name="notes" rows="2" placeholder="Побажання, вимоги до розміщення..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-700">Відправити замовлення</button>
                <a href="{{ route('client.orders.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
