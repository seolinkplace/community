@extends('client.layouts.app')
@section('title', $task->title)
@section('content')

    @php
        $userId   = auth('unified')->id();
        $userType = auth('unified')->check() ? \App\Models\UnifiedUser::class : null;
        $activeClaim = $userType ? \App\Models\TaskCompletion::where('task_id', $task->id)
            ->where('performer_id', $userId)
            ->where('performer_type', $userType)
            ->where('status', 'claimed')
            ->first() : null;
        $canPerform = !$isCreator
            && $task->status === 'active'
            && !$task->isExpired()
            && $task->hasSlots();
    @endphp

<div class="max-w-6xl mx-auto py-6 px-4">

    {{-- Заголовок --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.tasks.my') }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h1>
        @if($isCreator && !in_array($task->status, ['completed', 'cancelled']))
        <a href="{{ route('client.tasks.edit', $task) }}"
           class="ml-auto flex-shrink-0 text-xs px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600
                  text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            {{ __('client.task_edit') }}
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    {{-- Деталі завдання --}}
    @php
        $sc = match($task->status) {
            'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'paused'    => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
            default     => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
        };
        $statusLabel = match($task->status) {
            'active'    => __('client.status_active'),
            'paused'    => __('client.status_paused'),
            'completed' => __('client.status_completed_n'),
            'cancelled' => __('client.status_cancelled_n'),
            default     => $task->status,
        };
        $progress = $task->max_completions > 0
            ? round($task->completions_count / $task->max_completions * 100) : 0;
    @endphp

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_action_type') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->taskType?->name() ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.status') }}</p>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $sc }}">{{ $statusLabel }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_reward') }}</p>
                <p class="text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($task->reward, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_budget') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($task->budget_reserved, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_verification_type') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.verification_' . $task->verification_type) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_per_user_limit') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $task->per_user_limit === 0 ? __('client.task_per_user_limit_hint') : $task->per_user_limit }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_per_user_daily_limit') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $task->per_user_daily_limit === 0 ? __('client.task_per_user_limit_hint') : $task->per_user_daily_limit }}
                </p>
            </div>
            @if($task->expires_at)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_deadline_label') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->expires_at->format('d.m.Y H:i') }}</p>
            </div>
            @endif
        </div>

        @if($task->description)
        <div class="mb-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_description') }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->description }}</p>
        </div>
        @endif

        <div class="mb-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('client.task_url') }}</p>
            <a href="{{ $task->url }}" target="_blank" class="text-sm text-blue-500 hover:underline break-all">{{ $task->url }}</a>
        </div>

        @if($task->verification_instructions)
        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
            <p class="text-xs font-medium text-amber-700 dark:text-amber-400 mb-1">{{ __('client.task_verification_instructions') }}:</p>
            <p class="text-sm text-amber-600 dark:text-amber-300">{{ $task->verification_instructions }}</p>
        </div>
        @endif

        {{-- Прогрес --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mb-1">
                <span>{{ __('client.task_completions') }}: {{ $task->completions_count }}/{{ $task->max_completions }}</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>


    {{-- Claim / Submit block — для виконавців --}}
    @if($canPerform)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">

        @if($activeClaim)
            {{-- Є активний claim — показуємо таймер і форму здачі --}}
            @if($activeClaim->claim_expires_at)
            <div class="mb-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400"
                 x-data="claimTimer('{{ $activeClaim->claim_expires_at->toIso8601String() }}')"
                 x-init="start()">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ __('client.task_claim_expires') }}:</span>
                <span class="font-mono font-semibold" x-text="remaining"></span>
            </div>
            @endif

            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('client.task_submit_proof') }}
            </h3>
            <form method="POST" action="{{ route('client.tasks.complete', $task) }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_proof_link') }}
                    </label>
                    <input type="url" name="proof_url" value="{{ old('proof_url') }}"
                           placeholder="https://"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_screenshot_link') }}
                    </label>
                    <input type="file" name="proof_screenshot" accept="image/*"
                        class="w-full text-sm text-gray-700 dark:text-gray-300
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                               file:text-sm file:bg-indigo-50 file:text-indigo-700
                               dark:file:bg-indigo-950/30 dark:file:text-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.task_comment') }}
                    </label>
                    <textarea name="comment" rows="2"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                </div>
                <button type="submit"
                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           rounded-lg transition-colors text-sm">
                    {{ __('client.task_submit_proof') }}
                </button>
            </form>

        @else
            {{-- Немає claim — показуємо кнопку "Взяти завдання" --}}
            @if($task->claim_duration_minutes)
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ __('client.task_claim_duration') }}:
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ __('client.task_claim_duration_' . ($task->claim_duration_minutes >= 60
                        ? ($task->claim_duration_minutes >= 1440
                            ? ($task->claim_duration_minutes/1440).'h'
                            : ($task->claim_duration_minutes/60).'h')
                        : '10m')) }}
                </span>
            </p>
            @endif
            <form method="POST" action="{{ route('client.tasks.claim', $task) }}">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           rounded-lg transition-colors text-sm">
                    {{ __('client.task_claim_btn') }}
                </button>
            </form>
        @endif
    </div>
    @endif

    {{-- Список виконань --}}
    <h2 class="text-base font-bold text-gray-900 dark:text-white mb-3">
        @if($isCreator)
            {{ __('client.task_completions') }}
            <span class="text-sm font-normal text-gray-400">({{ $task->allCompletions->count() }})</span>
        @else
            {{ __('client.my_completions') }}
        @endif
    </h2>

    @forelse($task->allCompletions->sortByDesc('created_at') as $completion)
    @php
        $cs = match($completion->status) {
            'pending'  => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('client.status_pending')],
            'approved' => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',  __('client.status_approved')],
            'rejected' => ['bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',          __('client.status_rejected')],
            default    => ['bg-gray-100 dark:bg-gray-700 text-gray-500', $completion->status],
        };
        $performer = \App\Models\UnifiedUser::find($completion->performer_id);
    @endphp
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-3">
        <div class="flex items-start justify-between gap-4 mb-3">
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $cs[0] }}">{{ $cs[1] }}</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $performer?->name ?? $performer?->email ?? 'ID:'.$completion->performer_id }}
                </span>
                @if($performer?->email)
                <span class="text-xs text-gray-400 dark:text-gray-500 truncate hidden sm:block">{{ $performer->email }}</span>
                @endif
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $completion->created_at->format('d.m.Y H:i') }}</p>
                @if($completion->reward_paid)
                <p class="text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($completion->reward_paid, 2) }}</p>
                @endif
            </div>
        </div>

        {{-- Докази --}}
        <div class="flex flex-wrap gap-3 mb-3">
            @if($completion->proof_url)
            <a href="{{ $completion->proof_url }}" target="_blank"
               class="inline-flex items-center gap-1 text-xs text-blue-500 dark:text-blue-400 hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                {{ __('client.task_proof_link') }}
            </a>
            @endif
            @if($completion->proof_screenshot)
            <a href="{{ Storage::url($completion->proof_screenshot) }}" target="_blank"
               class="inline-flex items-center gap-1 text-xs text-blue-500 dark:text-blue-400 hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ __('client.task_screenshot_link') }}
            </a>
            @endif
        </div>

        @if($completion->comment)
        <div class="mb-3 p-2.5 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">{{ __('client.task_comment') }}:</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $completion->comment }}</p>
        </div>
        @endif

        @if($completion->creator_note)
        <div class="mb-3 p-2.5 bg-red-50 dark:bg-red-900/20 rounded-lg">
            <p class="text-xs text-red-500 mb-0.5">{{ __('client.task_rejection_note') }}:</p>
            <p class="text-sm text-red-700 dark:text-red-300">{{ $completion->creator_note }}</p>
        </div>
        @endif

        {{-- Approve/Reject кнопки — тільки для замовника --}}
        @if($isCreator && $completion->status === 'pending')
        <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
            <form method="POST" action="{{ route('client.tasks.review', $completion) }}">
                @csrf
                <input type="hidden" name="action" value="approve">
                <button class="text-xs bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg">
                    {{ __('client.approve') }}
                </button>
            </form>
            <form method="POST" action="{{ route('client.tasks.review', $completion) }}">
                @csrf
                <input type="hidden" name="action" value="reject">
                <button class="text-xs border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-4 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                    {{ __('client.reject') }}
                </button>
            </form>
            {{-- Скарга замовника на виконавця --}}
            @if(!$completion->complaints()->where('reporter_id', auth('unified')->id())->exists())
            <button
                x-data
                @click="$dispatch('open-complaint', { completionId: '{{ $completion->uuid }}' })"
                class="ml-auto text-xs border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 px-3 py-1.5 rounded-lg hover:border-red-300 hover:text-red-500 transition-colors">
                {{ __('client.complaint_btn') }}
            </button>
            @endif
        </div>
        @endif

        {{-- Скарга виконавця — після rejected або pending --}}
        @php
            $currentUserId = auth('unified')->id();
            $isThisPerformer = $completion->performer_id === $currentUserId
                && $completion->performer_type === \App\Models\UnifiedUser::class;
            $alreadyComplained = $completion->complaints()->where('reporter_id', $currentUserId)->exists();
        @endphp
        @if(!$isCreator && $isThisPerformer && in_array($completion->status, ['pending', 'rejected']) && !$alreadyComplained)
        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
            <button
                x-data
                @click="$dispatch('open-complaint', { completionId: '{{ $completion->uuid }}' })"
                class="text-xs border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 px-3 py-1.5 rounded-lg hover:border-red-300 hover:text-red-500 transition-colors">
                {{ __('client.complaint_btn') }}
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-400 text-sm">
        {{ __('client.no_completions') }}
    </div>
    @endforelse
</div>
@endsection

@push('modals')
{{-- Модалка скарги --}}
<div
    x-data="complaintModal()"
    x-show="open"
    x-on:open-complaint.window="openWith($event.detail.completionId)"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    style="display:none">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    {{-- Modal --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 shadow-xl"
         @click.stop>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('client.complaint_title') }}</h3>
            <button @click="open = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" :action="formAction" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.complaint_reason') }}
                </label>
                <select name="reason" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('client.complaint_reason_select') }}</option>
                    <option value="task_unclear">{{ __('client.complaint_reason_task_unclear') }}</option>
                    <option value="unfair_rejection">{{ __('client.complaint_reason_unfair_rejection') }}</option>
                    <option value="payment_issue">{{ __('client.complaint_reason_payment_issue') }}</option>
                    <option value="task_impossible">{{ __('client.complaint_reason_task_impossible') }}</option>
                    <option value="other">{{ __('client.complaint_reason_other') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('client.complaint_comment') }}
                </label>
                <textarea name="comment" rows="3" maxlength="1000"
                    placeholder="{{ __('client.complaint_comment_placeholder') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="open = false"
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300
                           rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    {{ __('common.cancel') }}
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold
                           rounded-lg text-sm transition-colors">
                    {{ __('client.complaint_submit') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
function complaintModal() {
    return {
        open: false,
        formAction: '',
        openWith(completionId) {
            // Визначаємо prefix з поточного URL
            const path = window.location.pathname;
            let prefix = 'app';
            if (path.startsWith('/wm/')) prefix = 'wm';
            else if (path.startsWith('/performer/')) prefix = 'performer';
            this.formAction = '/' + prefix + '/tasks/completions/' + completionId + '/complaint';
            this.open = true;
        }
    }
}

function claimTimer(expiresAt) {
    return {
        remaining: '',
        interval: null,
        start() {
            this.tick();
            this.interval = setInterval(() => this.tick(), 1000);
        },
        tick() {
            const diff = Math.floor((new Date(expiresAt) - new Date()) / 1000);
            if (diff <= 0) {
                this.remaining = '00:00:00';
                clearInterval(this.interval);
                return;
            }
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            this.remaining = `${h}:${m}:${s}`;
        }
    }
}
</script>
@endpush

