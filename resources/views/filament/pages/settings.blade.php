<x-filament-panels::page>
    @php
        $regEnabled = \App\Models\Setting::get('registration_enabled', true);
        $firstPost  = \App\Models\Setting::get('first_post_required_global', true);
    @endphp

    {{-- Реєстрація --}}
    <x-filament::section>
        <x-slot name="heading">Реєстрація користувачів</x-slot>
        <x-slot name="description">Дозволяє або забороняє реєстрацію нових акаунтів.</x-slot>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <x-filament::badge :color="$regEnabled ? 'success' : 'danger'">{{ $regEnabled ? 'Відкрита' : 'Закрита' }}</x-filament::badge>
            <x-filament::button wire:click="toggleRegistration" :color="$regEnabled ? 'danger' : 'success'">
                {{ $regEnabled ? 'Закрити реєстрацію' : 'Відкрити реєстрацію' }}
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- Перша стаття --}}
    <x-filament::section>
        <x-slot name="heading">Вимога першої статті про {{ config('app.name') }}</x-slot>
        <x-slot name="description">Вебмастер повинен опублікувати статтю перед активацією сайту.</x-slot>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <x-filament::badge :color="$firstPost ? 'success' : 'gray'">{{ $firstPost ? 'Увімкнено' : 'Вимкнено' }}</x-filament::badge>
            <x-filament::button wire:click="toggleFirstPost" :color="$firstPost ? 'warning' : 'success'">
                {{ $firstPost ? 'Вимкнути вимогу' : 'Увімкнути вимогу' }}
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- Email налаштування --}}
    <x-filament::section>
        <x-slot name="heading">Email нотифікації</x-slot>
        <x-slot name="description">Керуйте які листи відправляються користувачам.</x-slot>
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($emailSettings as $es)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--gray-200, #e5e7eb);">
                <div>
                    <div style="font-weight:500;font-size:14px;">{{ $es->label }}</div>
                    <div style="font-size:12px;color:#6b7280;">{{ $es->key }}</div>
                </div>
                <x-filament::button
                    size="sm"
                    wire:click="toggleEmail('{{ $es->key }}')"
                    :color="$es->enabled ? 'success' : 'gray'">
                    {{ $es->enabled ? 'Увімкнено' : 'Вимкнено' }}
                </x-filament::button>
            </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- Статистика email сьогодні --}}
    <x-filament::section>
        <x-slot name="heading">Статистика email — сьогодні</x-slot>
        @if($statsToday->isEmpty())
        <p style="color:#6b7280;font-size:14px;">Сьогодні листів не відправлялось.</p>
        @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead><tr style="border-bottom:1px solid #e5e7eb;">
                <th style="text-align:left;padding:6px 0;color:#6b7280;">Тип</th>
                <th style="text-align:left;padding:6px 0;color:#6b7280;">Статус</th>
                <th style="text-align:right;padding:6px 0;color:#6b7280;">К-сть</th>
            </tr></thead>
            <tbody>
            @foreach($statsToday as $s)
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:6px 0;">{{ $s->type }}</td>
                <td style="padding:6px 0;">
                    <span style="color:{{ $s->status === 'sent' ? '#16a34a' : ($s->status === 'failed' ? '#dc2626' : '#9ca3af') }}">{{ $s->status }}</span>
                </td>
                <td style="padding:6px 0;text-align:right;font-weight:600;">{{ $s->cnt }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </x-filament::section>

    {{-- Статистика за місяць --}}
    <x-filament::section>
        <x-slot name="heading">Статистика email — цей місяць</x-slot>
        @if($statsMonth->isEmpty())
        <p style="color:#6b7280;font-size:14px;">Цього місяця листів не відправлялось.</p>
        @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead><tr style="border-bottom:1px solid #e5e7eb;">
                <th style="text-align:left;padding:6px 0;color:#6b7280;">Тип</th>
                <th style="text-align:left;padding:6px 0;color:#6b7280;">Статус</th>
                <th style="text-align:right;padding:6px 0;color:#6b7280;">К-сть</th>
            </tr></thead>
            <tbody>
            @foreach($statsMonth as $s)
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:6px 0;">{{ $s->type }}</td>
                <td style="padding:6px 0;">
                    <span style="color:{{ $s->status === 'sent' ? '#16a34a' : ($s->status === 'failed' ? '#dc2626' : '#9ca3af') }}">{{ $s->status }}</span>
                </td>
                <td style="padding:6px 0;text-align:right;font-weight:600;">{{ $s->cnt }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </x-filament::section>

    {{-- Останні помилки --}}
    @if($failedRecent->isNotEmpty())
    <x-filament::section>
        <x-slot name="heading">Останні помилки відправки</x-slot>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($failedRecent as $log)
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;font-size:13px;">
                <div style="font-weight:600;color:#991b1b;">{{ $log->type }} — {{ $log->created_at->format('d.m.Y H:i') }}</div>
                <div style="color:#7f1d1d;margin-top:4px;">{{ $log->error }}</div>
            </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

</x-filament-panels::page>
