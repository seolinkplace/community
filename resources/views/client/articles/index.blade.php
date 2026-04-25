@extends('client.layouts.app')
@section('title', __('client.articles_title'))
@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('client.articles_title') }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('client.articles.catalog') }}"
               class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                {{ __('client.go_to_sites_catalog') }}
            </a>
            <a href="{{ route('client.articles.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('client.articles_submit') }}
            </a>
        </div>
    </div>

    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach([
            ''                   => __('client.filter_all'),
            'submitted'          => __('client.filter_submitted'),
            'approved'           => __('client.filter_approved'),
            'published'          => __('client.filter_published'),
            'rejected'           => __('client.filter_rejected'),
            'revision_requested' => __('client.filter_revision'),
            'draft'              => __('client.filter_drafts'),
        ] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="text-xs px-3 py-1.5 rounded-lg border {{ request('status', '') === $val ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    @forelse($articles as $article)
    @php
        $sc = match($article->status) {
            'submitted'          => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'approved'           => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'published'          => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'rejected'           => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
            'revision_requested' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
            default               => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
        };
        $sl = match($article->status) {
            'draft'              => __('client.status_draft'),
            'submitted'          => __('client.status_submitted'),
            'approved'           => __('client.status_approved'),
            'published'          => __('client.status_published'),
            'rejected'           => __('client.status_rejected'),
            'revision_requested' => __('client.status_revision_requested'),
            default               => $article->status,
        };
    @endphp
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-3">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <a href="{{ route('client.articles.show', $article) }}" class="text-sm font-semibold hover:underline text-gray-900 dark:text-white">{{ $article->title ?: __('client.articles_no_title') }}</a>
                <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 dark:text-gray-500 flex-wrap">
                    <span>{{ __('client.site_col') }} <strong class="text-gray-600 dark:text-gray-300">{{ $article->site ? ($article->site->trashed() ? __('client.site_unavailable') : $article->site->domain) : '—' }}</strong></span>
                    <span>{{ $article->created_at->format('d.m.Y H:i') }}</span>
                    @if($article->published_url)
                    <a href="{{ $article->published_url }}" target="_blank" class="text-green-600 dark:text-green-400 hover:underline">{{ __('client.articles_published_link') }} →</a>
                    @endif
                </div>
                @if($article->status === 'rejected' && $article->notes)
                <div class="mt-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded px-3 py-1.5 text-xs text-red-700 dark:text-red-400">
                    {{ __('client.articles_reject_reason') }}: {{ $article->notes }}
                </div>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
                @if(in_array($article->status, ['draft', 'rejected']))
                <a href="{{ route('client.articles.edit', $article) }}"
                   class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('client.edit') }}
                </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
        <p class="text-gray-400 dark:text-gray-500 text-sm mb-2">{{ __('client.articles_empty') }}</p>
        <a href="{{ route('client.articles.catalog') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">{{ __('client.go_to_sites_catalog') }}</a>
    </div>
    @endforelse

    @if($articles->hasPages())
    <div class="mt-4">{{ $articles->links() }}</div>
    @endif
</div>
@endsection
