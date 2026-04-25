@extends('client.layouts.app')
@section('title', __('client.edit_link'))

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('client.edit_link') }}</h1>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('client.links.update', $link) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL донора *</label>
                    <input type="url" name="donor_url" value="{{ old('donor_url', $link->donor_url) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Цільова сторінка *</label>
                    <input type="url" name="target_url" value="{{ old('target_url', $link->target_url) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Анкор</label>
                    <input type="text" name="anchor" value="{{ old('anchor', $link->anchor) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Тип *</label>
                        <select name="link_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="dofollow" {{ old('link_type', $link->link_type) === 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                            <option value="nofollow" {{ old('link_type', $link->link_type) === 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ціна ($)</label>
                        <input type="number" name="price" value="{{ old('price', $link->price) }}" step="0.01" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Проект</label>
                        <input type="text" name="project" value="{{ old('project', $link->project) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.modal_campaign') }}</label>
                        <input type="text" name="campaign" value="{{ old('campaign', $link->campaign) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата розміщення</label>
                    <input type="date" name="placement_date" value="{{ old('placement_date', $link->placement_date?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                @if($sites->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Прив'язати до сайту</label>
                    <select name="site_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">— не вибрано —</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $link->site_id) == $site->id ? 'selected' : '' }}>{{ $site->domain }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Нотатки</label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('notes', $link->notes) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-700">Зберегти</button>
                <a href="{{ route('client.links.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
