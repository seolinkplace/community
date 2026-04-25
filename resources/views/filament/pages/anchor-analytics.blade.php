<x-filament-panels::page>
    <div x-data style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Фільтри --}}
        <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;padding:1rem;background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:0.75rem;">
            <div>
                <div style="font-size:0.75rem;font-weight:500;margin-bottom:0.25rem;opacity:0.7;">Сайт</div>
                <select wire:model.live="site_id" style="border:1px solid var(--color-border);border-radius:0.5rem;padding:0.4rem 0.75rem;font-size:0.875rem;background:var(--color-bg-base);color:inherit;">
                    <option value="">Всі сайти</option>
                    @foreach($this->getSiteConnections() as $id => $url)
                        <option value="{{ $id }}">{{ $url }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div style="font-size:0.75rem;font-weight:500;margin-bottom:0.25rem;opacity:0.7;">Пошук</div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Анкор..."
                    style="border:1px solid var(--color-border);border-radius:0.5rem;padding:0.4rem 0.75rem;font-size:0.875rem;background:var(--color-bg-base);color:inherit;width:200px;">
            </div>
            <div>
                <div style="font-size:0.75rem;font-weight:500;margin-bottom:0.25rem;opacity:0.7;">Мін. сторінок</div>
                <input type="number" wire:model.live="min_count" min="1"
                    style="border:1px solid var(--color-border);border-radius:0.5rem;padding:0.4rem 0.75rem;font-size:0.875rem;background:var(--color-bg-base);color:inherit;width:80px;">
            </div>
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.875rem;cursor:pointer;">
                <input type="checkbox" wire:model.live="hide_spam"> Фільтр сміття
            </label>
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.875rem;cursor:pointer;">
                <input type="checkbox" wire:model.live="hide_used"> Тільки вільні
            </label>
        </div>

        @php $anchors = $this->getAnchors(); @endphp

        {{-- Статистика --}}
        <div style="display:flex;gap:1.5rem;font-size:0.875rem;">
            <span>Всього: <strong>{{ $anchors->count() }}</strong></span>
            <span style="color:#16a34a;">Зайнятих: <strong>{{ $anchors->where('is_used', true)->count() }}</strong></span>
            <span style="color:#2563eb;">Вільних: <strong>{{ $anchors->where('is_used', false)->count() }}</strong></span>
        </div>

        {{-- Таблиця --}}
        <div style="border:1px solid var(--color-border);border-radius:0.75rem;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                <thead>
                    <tr style="background:var(--color-bg-surface);border-bottom:1px solid var(--color-border);">
                        <th style="text-align:left;padding:0.6rem 1rem;font-weight:500;opacity:0.7;">Анкор</th>
                        <th style="text-align:left;padding:0.6rem 1rem;font-weight:500;opacity:0.7;">Сайт</th>
                        <th style="text-align:center;padding:0.6rem 0.75rem;font-weight:500;opacity:0.7;">Сторінок</th>
                        <th style="text-align:left;padding:0.6rem 0.75rem;font-weight:500;opacity:0.7;">Статус</th>
                        <th style="text-align:left;padding:0.6rem 0.75rem;font-weight:500;opacity:0.7;">Дія</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anchors as $anchor)
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <td style="padding:0.5rem 1rem;font-weight:500;">{{ $anchor->anchor_text }}</td>
                        <td style="padding:0.5rem 1rem;font-size:0.75rem;opacity:0.5;">{{ parse_url($anchor->wp_url, PHP_URL_HOST) }}</td>
                        <td style="padding:0.5rem 0.75rem;text-align:center;">
                            <span style="background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:0.25rem;padding:0.1rem 0.4rem;font-size:0.75rem;font-family:monospace;">{{ $anchor->total_count }}</span>
                        </td>
                        <td style="padding:0.5rem 0.75rem;">
                            @if($anchor->is_used)
                                <span style="background:#dcfce7;color:#166534;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.75rem;">Зайнятий</span>
                            @else
                                <span style="background:#dbeafe;color:#1e40af;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.75rem;">Вільний</span>
                            @endif
                        </td>
                        <td style="padding:0.5rem 0.75rem;">
                            @if(!$anchor->is_used)
                                <a href="/app/links/create?anchor={{ urlencode($anchor->anchor_text) }}" target="_blank"
                                    style="font-size:0.75rem;color:#2563eb;text-decoration:none;">+ Посилання</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:2rem;text-align:center;opacity:0.4;">Анкорів не знайдено</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
