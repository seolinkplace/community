@extends('webmaster.layouts.app')
@section('title', $article->title ?: __('client.articles_no_title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">

    <a href="{{ route('webmaster.articles.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-4">
        ← {{ __('client.back') }}
    </a>

    {{-- Title + status --}}
    <div class="flex items-start justify-between gap-3 mb-4">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white flex-1">{{ $article->title ?: __('client.articles_no_title') }}</h1>
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
        <span class="flex-shrink-0 text-xs px-2 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
    </div>

    {{-- Meta --}}
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400 dark:text-gray-500 mb-4">
        <span class="font-medium text-gray-600 dark:text-gray-300">{{ $article->site->domain ?? '—' }}</span>
        <span class="text-gray-300 dark:text-gray-600">·</span>
        <span>{{ $article->created_at->format('d.m.Y H:i') }}</span>
        @if($article->published_url)
        <span class="text-gray-300 dark:text-gray-600">·</span>
        <a href="{{ $article->published_url }}" target="_blank" class="text-green-500 dark:text-green-400 hover:underline">{{ __('client.articles_published_link') }} →</a>
        @endif
    </div>

    {{-- Rejection reason / notes --}}
    @if($article->status === 'rejected' && $article->notes)
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 text-sm text-red-700 dark:text-red-400">
        <strong>{{ __('client.articles_reject_reason') }}:</strong> {{ $article->notes }}
    </div>
    @elseif($article->notes)
    <div class="mb-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
        {{ $article->notes }}
    </div>
    @endif

    {{-- Revision comment --}}
    @if($article->status === 'revision_requested' && $article->revision_comment)
    <div class="mb-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg px-4 py-3">
        <p class="text-xs font-medium text-orange-700 dark:text-orange-400 mb-1">{{ __('client.revision_comment') }}:</p>
        <p class="text-sm text-orange-800 dark:text-orange-300">{{ $article->revision_comment }}</p>
    </div>
    @endif

    {{-- Rating (published only) --}}
    @if($article->status === 'published')
    @php $rating = $article->ratings()->where('rated_by', 'webmaster')->first(); @endphp
    <form method="POST" action="{{ route('webmaster.articles.rate', $article) }}"
          class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-4">
        @csrf
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.rate_article') }}</p>
        <div class="flex flex-wrap items-center gap-6">
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ __('client.text_quality') }}</p>
                <div class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setStar('text_quality', {{ $i }})"
                            class="text-2xl leading-none {{ ($rating?->text_quality >= $i) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} hover:text-yellow-400 transition-colors"
                            data-group="text_quality" data-val="{{ $i }}">★</button>
                    @endfor
                    <input type="hidden" name="text_quality" id="input-text_quality" value="{{ $rating?->text_quality ?? '' }}">
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ __('client.layout_quality') }}</p>
                <div class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setStar('layout_quality', {{ $i }})"
                            class="text-2xl leading-none {{ ($rating?->layout_quality >= $i) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} hover:text-yellow-400 transition-colors"
                            data-group="layout_quality" data-val="{{ $i }}">★</button>
                    @endfor
                    <input type="hidden" name="layout_quality" id="input-layout_quality" value="{{ $rating?->layout_quality ?? '' }}">
                </div>
            </div>
            <div class="flex-1 min-w-48">
                <input type="text" name="comment" value="{{ $rating?->comment }}"
                       placeholder="{{ __('client.rating_comment_placeholder') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
            </div>
            <button type="submit" class="text-sm bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg whitespace-nowrap">
                {{ __('client.save_rating') }}
            </button>
        </div>
    </form>
    @endif

    {{-- Article content / brief --}}
    @if($article->type === 'webmaster_writes')
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 mb-4">
        <div class="px-5 pt-4 pb-3">
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ __('client.article_type_wm_writes') }}</span>
        </div>
        <div class="px-5 pb-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.article_brief_label') }}</p>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $article->brief }}</div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 mb-4">
        <div class="flex items-center justify-between px-5 pt-4 pb-3">
            <span class="text-xs text-gray-400">{{ __('client.article_content') }}</span>
            <button onclick="toggleHtml()" id="htmlToggleBtn"
                class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                {{ __('client.article_html') }}
            </button>
        </div>
        <div id="articleRendered" class="prose prose-sm dark:prose-invert max-w-none p-5">{!! $article->content !!}</div>
        <pre id="articleHtml" class="hidden mt-4 mx-5 mb-5 p-4 text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 overflow-x-auto rounded-xl whitespace-pre-wrap break-all">{{ $article->content }}</pre>
    </div>
    @endif

    {{-- Actions: submitted --}}
    @if($article->status === 'submitted')
    <div class="flex flex-wrap gap-2 mb-4">
        <form method="POST" action="{{ route('webmaster.articles.approve', $article) }}">
            @csrf
            <button class="inline-flex items-center gap-2 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('client.approve') }}
            </button>
        </form>
        <button onclick="openRejectModal('{{ $article->uuid }}')"
                class="inline-flex items-center gap-2 text-sm border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-4 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ __('client.reject') }}
        </button>
    </div>
    @endif

    {{-- Actions: approved → publish --}}
    @if($article->status === 'approved')
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('client.article_mark_published') }}</h3>
        <form method="POST" action="{{ route('webmaster.articles.publish', $article) }}" class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.published_url_label') }}</label>
                <input type="url" name="published_url" required
                       placeholder="https://example.com/your-article"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('client.article_publish') }}
            </button>
        </form>
    </div>
    @endif

    {{-- Actions: revision_requested --}}
    @if($article->status === 'revision_requested')
    <div class="flex flex-wrap gap-2 mb-4">
        <form method="POST" action="{{ route('webmaster.articles.revised', $article) }}">
            @csrf
            <button class="inline-flex items-center gap-2 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('client.revision_done') }}
            </button>
        </form>
    </div>
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
function setStar(group, val) {
    document.getElementById('input-' + group).value = val;
    document.querySelectorAll('[data-group="' + group + '"]').forEach(btn => {
        const isActive = parseInt(btn.dataset.val) <= val;
        btn.classList.toggle('text-yellow-400', isActive);
        btn.classList.toggle('text-gray-300', !isActive);
        btn.classList.toggle('dark:text-gray-600', !isActive);
    });
}
function toggleHtml() {
    const r = document.getElementById('articleRendered');
    const h = document.getElementById('articleHtml');
    const b = document.getElementById('htmlToggleBtn');
    if (h.classList.contains('hidden')) {
        h.classList.remove('hidden'); r.classList.add('hidden'); b.classList.add('bg-gray-200','dark:bg-gray-700');
    } else {
        h.classList.add('hidden'); r.classList.remove('hidden'); b.classList.remove('bg-gray-200','dark:bg-gray-700');
    }
}
function openRejectModal(uuid) {
    document.getElementById('rejectForm').action = '/wm/articles/' + uuid + '/reject';
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
function setReason(text) {
    document.getElementById('rejectReason').value = text;
}
</script>
@endsection
