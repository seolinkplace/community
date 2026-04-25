@extends('webmaster.layouts.app')
@section('title', __('client.articles_title'))
@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">

    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('client.articles_title') }}</h1>

    <div class="flex flex-wrap gap-2 mb-4">
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
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3 mb-4">
            {{ session('error') }}
        </div>
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
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-3">
        <div class="flex items-start justify-between gap-2 mb-2">
            <a href="{{ route('webmaster.articles.show', $article) }}" class="text-sm font-semibold text-gray-900 dark:text-white flex-1 hover:underline">{{ $article->title ?: __('client.articles_no_title') }}</a>
            <span class="flex-shrink-0 text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400 dark:text-gray-500 mb-3">
            <span class="font-medium text-gray-600 dark:text-gray-300">{{ $article->site->domain ?? '—' }}</span>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <span>{{ $article->created_at->format('d.m.Y H:i') }}</span>
            @if($article->published_url)
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <a href="{{ $article->published_url }}" target="_blank" class="text-green-500 dark:text-green-400 hover:underline">{{ __('client.articles_published_link') }} →</a>
            @endif
        </div>

        @if($article->status === 'rejected' && $article->notes)
        <div class="mb-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-3 py-2 text-xs text-red-700 dark:text-red-400">
            <strong>{{ __('client.articles_reject_reason') }}:</strong> {{ $article->notes }}
        </div>
        @elseif($article->status !== 'rejected' && $article->notes)
        <p class="text-xs text-gray-400 dark:text-gray-500 italic mb-3">{{ $article->notes }}</p>
        @endif

        @if($article->status === 'submitted')
        <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
            <form method="POST" action="{{ route('webmaster.articles.approve', $article) }}">
                @csrf
                <button class="inline-flex items-center gap-1.5 text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('client.approve') }}
                </button>
            </form>
            <button onclick="openRejectModal('{{ $article->uuid }}')"
                    class="inline-flex items-center gap-1.5 text-xs border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ __('client.reject') }}
            </button>
        </div>
        @endif

        @if($article->status === 'approved')
        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('webmaster.articles.show', $article) }}"
               class="inline-flex items-center gap-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.172 13.828a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.102-1.101"/></svg>
                {{ __('client.article_publish') }}
            </a>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
        <p class="text-gray-400 text-sm">{{ __('client.articles_empty') }}</p>
    </div>
    @endforelse

    @if($articles->hasPages())
    <div class="mt-4">{{ $articles->links() }}</div>
    @endif
</div>

{{-- Reject modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeRejectModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md p-6 relative">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('client.articles_reject_title') }}</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.articles_quick_reasons') }}:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach([
                                __('client.articles_reason_1'),
                                __('client.articles_reason_2'),
                                __('client.articles_reason_3'),
                                __('client.articles_reason_4'),
                                __('client.articles_reason_5'),
                                __('client.articles_reason_6'),
                            ] as $preset)
                            <button type="button" onclick="setReason('{{ addslashes($preset) }}')"
                                    class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-left">
                                {{ $preset }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            {{ __('client.articles_reject_reason') }} <span class="text-gray-400">({{ __('client.articles_visible_to_client') }})</span>
                        </label>
                        <textarea name="reason" id="rejectReason" rows="3"
                                  placeholder="{{ __('client.articles_reject_placeholder') }}"
                                  class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg text-sm">
                            {{ __('client.reject') }}
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                                class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium py-2 rounded-lg text-sm">
                            {{ __('client.cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openRejectModal(uuid) {
    document.getElementById('rejectForm').action = '/wm/articles/' + uuid + '/reject';
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }
function setReason(text) { document.getElementById('rejectReason').value = text; }
</script>
@endsection
