@php
$platforms = [
    'website'   => 'Website',
    'telegram'  => 'Telegram',
    'instagram' => 'Instagram',
    'facebook'  => 'Facebook',
    'youtube'   => 'YouTube',
    'tiktok'    => 'TikTok',
    'linkedin'  => 'LinkedIn',
    'x'         => 'X (Twitter)',
    'threads'   => 'Threads',
    'pinterest' => 'Pinterest',
    'reddit'    => 'Reddit',
    'bluesky'   => 'Bluesky',
    'other'     => __('client.platform_other'),
];
$currentPlatform = old('platform_type', $site->platform_type ?? 'website');
$locked = $platformLocked ?? false;
@endphp
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('client.platform_type') }} *</label>
    <select name="platform_type" id="platform_type" onchange="updatePlatformFields()"
        {{ $locked ? 'disabled' : '' }}
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 {{ $locked ? 'opacity-60 cursor-not-allowed bg-gray-50 dark:bg-gray-700' : '' }}">
        @foreach($platforms as $val => $label)
            <option value="{{ $val }}" {{ $currentPlatform === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @if($locked)
        <input type="hidden" name="platform_type" value="{{ $currentPlatform }}">
        <p class="text-xs text-gray-400 mt-1">{{ __('client.platform_locked_hint') }}</p>
    @endif
</div>
