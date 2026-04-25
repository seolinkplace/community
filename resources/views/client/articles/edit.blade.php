@extends('client.layouts.app')
@section('title', __('client.edit_article'))
@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.articles.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-sm">← {{ __('client.articles_title') }}</a>
        <span class="text-gray-300 dark:text-gray-600">/</span>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.edit_article') }}</h1>
    </div>

    @if($article->status === 'rejected' && $article->notes)
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 text-sm text-red-700 dark:text-red-400">
        {{ __('client.articles_reject_reason') }}: <strong>{{ $article->notes }}</strong>
    </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('client.articles.update', $article) }}"
          class="bg-white dark:bg-gray-900 rounded-xl shadow p-6 space-y-5">
        @csrf @method('PUT')

        {{-- Site (read-only) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.site_label') }}</label>
            <div class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg px-3 py-2 text-sm">
                {{ $article->site?->domain ?? '—' }}
            </div>
        </div>

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.article_title_label') }}</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Content --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.article_content') }}</label>
            <textarea name="content" rows="10"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('content', $article->content) }}</textarea>
        </div>

        {{-- Notes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.article_notes_label') }}</label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes', $article->notes) }}</textarea>
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                {{ __('client.articles_save_and_submit') }}
            </button>
            <a href="{{ route('client.articles.show', $article) }}"
               class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium px-5 py-2 rounded-lg text-sm">
                {{ __('client.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
