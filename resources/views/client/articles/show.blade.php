@extends('client.layouts.app')
@section('title', $article->title ?: __('client.articles_no_title'))
@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">

    <a href="{{ route('client.articles.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-4">
        ← {{ __('client.back') }}
    </a>

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

    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400 dark:text-gray-500 mb-4">
        <span class="font-medium text-gray-600 dark:text-gray-300">{{ $article->site ? ($article->site->trashed() ? __('client.site_unavailable') : $article->site->domain) : '—' }}</span>
        <span class="text-gray-300 dark:text-gray-600">·</span>
        <span>{{ $article->created_at->format('d.m.Y H:i') }}</span>
        @if($article->published_url)
        <span class="text-gray-300 dark:text-gray-600">·</span>
        <a href="{{ $article->published_url }}" target="_blank" class="text-green-500 dark:text-green-400 hover:underline">{{ __('client.articles_published_link') }} →</a>
        @endif
    </div>

    @if($article->status === 'rejected' && $article->notes)
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 text-sm text-red-700 dark:text-red-400">
        <strong>{{ __('client.articles_reject_reason') }}:</strong> {{ $article->notes }}
    </div>
    @elseif($article->notes)
    <div class="mb-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
        {{ $article->notes }}
    </div>
    @endif

    {{-- Article type badge --}}
    <div class="mb-3">
        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
            {{ $article->type === 'webmaster_writes' ? __('client.article_type_wm_writes') : __('client.article_type_client_provides') }}
        </span>
    </div>

    {{-- Brief (webmaster_writes) or content (client_provides) --}}
    @if($article->type === 'webmaster_writes')
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 mb-4">
        <div class="px-5 pt-4 pb-3">
            <span class="text-xs font-medium text-gray-400">{{ __('client.article_brief_label') }}</span>
        </div>
        <div class="px-5 pb-5 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $article->brief }}</div>
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

    {{-- Edit button --}}
    @if(in_array($article->status, ['draft', 'rejected']))
    <div class="mb-4">
        <a href="{{ route('client.articles.edit', $article) }}"
           class="inline-flex items-center gap-2 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ __('client.edit') }}
        </a>
    </div>
    @endif

    {{-- Revision request (published only) --}}
    @if($article->status === 'published')
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('client.request_revision') }}</h3>
        @php
            $connection    = \App\Models\SiteConnection::where('site_id', $article->site_id)->first();
            $freeRevisions = $connection?->free_revisions ?? 1;
            $revisionPrice = $connection?->revision_price;
            if (!$revisionPrice) {
                $pagePrice     = \App\Models\PagePrice::where('site_id', $article->site_id)
                    ->where('price_type', 'article_client')
                    ->where('scope_type', 'site_default')
                    ->first();
                $base          = (float)($pagePrice?->price_article_once ?? 0);
                $revisionPrice = round($base * 0.10, 2);
            }
            $isPaid = $article->revision_count >= $freeRevisions;
        @endphp
        @if($freeRevisions > 0 && !$isPaid)
        <p class="text-xs text-green-600 dark:text-green-400 mb-3">
            {{ __('client.free_revisions_left', ['count' => $freeRevisions - $article->revision_count]) }}
        </p>
        @else
        <p class="text-xs text-yellow-600 dark:text-yellow-400 mb-3">
            {{ __('client.revision_cost', ['price' => number_format($revisionPrice, 2)]) }}
        </p>
        @endif
        <form method="POST" action="{{ route('client.articles.revision', $article) }}">
            @csrf
            <textarea name="comment" rows="3" required
                      placeholder="{{ __('client.revision_comment_placeholder') }}"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm mb-3 resize-none focus:outline-none focus:ring-2 focus:ring-gray-400"></textarea>
            <button type="submit"
                    class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                {{ __('client.request_revision') }}
            </button>
        </form>
    </div>
    @endif

</div>
<script>
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
</script>
@endsection
