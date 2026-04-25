@extends('client.layouts.app')
@section('title', __('client.create_token_title'))

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('client.create_token') }}</h1>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('client.tokens.store') }}">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Домен сайту *</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" placeholder="example.com" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <p class="text-xs text-gray-400 mt-1">Домен сайту де буде встановлений сніпет</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ліміт посилань</label>
                        <input type="number" name="link_limit" value="{{ old('link_limit', 5) }}" min="1" max="100"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Тип посилань</label>
                        <select name="link_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="dofollow">Dofollow</option>
                            <option value="nofollow">Nofollow</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-700">{{ __('client.create') }}</button>
                <a href="{{ route('client.tokens.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
