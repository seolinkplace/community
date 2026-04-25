@extends('webmaster.layouts.app')
@section('title', __('client.edit_site'))

@section('content')
@php $activeTab = session('tab', 'main'); @endphp

    {{-- Верифікація сайту --}}
    @if(!$site->verified_at)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 mb-1">{{ __('client.site_not_verified') }}</h3>
                <p class="text-xs text-yellow-700 dark:text-yellow-500 mb-3">{{ __('client.site_verify_desc') }}</p>
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-yellow-200 dark:border-yellow-800 p-3 mb-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.verify_step1') }}</p>
                    <p class="text-xs font-mono text-gray-700 dark:text-gray-300 break-all">https://{{ $site->domain }}/seolinkplace-verify.txt</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 mb-1">{{ __('client.verify_step2') }}</p>
                    <code class="text-xs font-mono text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded block break-all">seolinkplace-verification={{ $site->verification_token }}</code>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <form method="POST" action="{{ route('webmaster.sites.verify', $site) }}">
                        @csrf
                        <button class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg">
                            {{ __('client.verify_check') }}
                        </button>
                    
<script>
function updatePlatformFields() {
    var el = document.getElementById('platform_type'); var t = el ? el.value : '{{ $site->platform_type ?? 'website' }}';
    var isSocial = t !== 'website';
    document.getElementById('field_platform_url').classList.toggle('hidden', !isSocial);
    document.getElementById('field_followers').classList.toggle('hidden', !isSocial);
    document.getElementById('field_domain').classList.toggle('hidden', isSocial);
}
</script>
</form>
                    <form method="POST" action="{{ route('webmaster.sites.regenerate-token', $site) }}">
                        @csrf
                        <button class="text-xs border border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 px-3 py-1.5 rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900/20">
                            {{ __('client.verify_new_token') }}
                        </button>
                    
<script>
function updatePlatformFields() {
    var el = document.getElementById('platform_type'); var t = el ? el.value : '{{ $site->platform_type ?? 'website' }}';
    var isSocial = t !== 'website';
    document.getElementById('field_platform_url').classList.toggle('hidden', !isSocial);
    document.getElementById('field_followers').classList.toggle('hidden', !isSocial);
    document.getElementById('field_domain').classList.toggle('hidden', isSocial);
}
</script>
</form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-5 py-3 mb-6 flex items-center gap-2">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm text-green-700 dark:text-green-400">{{ __('client.site_verified_on', ['date' => $site->verified_at->format('d.m.Y')]) }}</p>
    </div>
    @endif

<div class="max-w-4xl">

{{-- Tabs --}}
<div class="flex mb-6 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
    <button onclick="switchTab('main')" id="tab-btn-main"
        class="tab-btn whitespace-nowrap px-3 py-2 text-xs sm:text-sm font-medium rounded-t-lg border-b-2 transition-colors flex-shrink-0
               {{ $activeTab === 'main' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
        {{ __('client.tab_main_settings') }}
    </button>
    <button onclick="switchTab('link-block')" id="tab-btn-link-block"
        class="tab-btn whitespace-nowrap px-3 py-2 text-xs sm:text-sm font-medium rounded-t-lg border-b-2 transition-colors flex-shrink-0
               {{ $activeTab === 'link-block' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
        {{ __('client.tab_link_block') }}
    </button>
    <button onclick="switchTab('danger')" id="tab-btn-danger"
        class="tab-btn whitespace-nowrap px-3 py-2 text-xs sm:text-sm font-medium rounded-t-lg border-b-2 transition-colors flex-shrink-0 border-transparent text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400">
        {{ __('client.tab_danger_zone') }}
    </button>
</div>

{{-- Tab: Main --}}
<div id="tab-main" class="{{ $activeTab === 'link-block' ? 'hidden' : '' }}">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Редагувати: {{ $site->domain }}</h1>


    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('webmaster.sites.update', $site) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-5">
                @include('webmaster.sites.partials.platform-select')
                <div id="field_platform_url" class="{{ old('platform_type',$site->platform_type??'website')==='website' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.platform_url') }}</label>
                    <input type="url" name="platform_url" value="{{ old('platform_url', $site->platform_url) }}" placeholder="https://t.me/mychannel"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div id="field_followers" class="{{ old('platform_type',$site->platform_type??'website')==='website' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.followers_count') }}</label>
                    <input type="number" name="followers_count" value="{{ old('followers_count', $site->followers_count) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div id="field_domain" class="{{ old('platform_type',$site->platform_type??'website')!=='website' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_domain') }} *</label>
                    <input type="text" name="domain" value="{{ old('domain', $site->domain) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_niche') }}</label>
                        <input type="text" name="niche" value="{{ old('niche', $site->niche) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.site_languages') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($languages as $code => $label)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="languages[]" value="{{ $code }}"
                                       {{ in_array($code, $siteLangs) ? 'checked' : '' }}
                                       class="rounded border-gray-300">
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_dr') }}</label>
                        <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
                            {{ $site->dr ?? '—' }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('client.field_set_by_admin') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_traffic') }}</label>
                        <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
                            {{ $site->traffic ? number_format($site->traffic) : '—' }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('client.field_set_by_admin') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_content_type') }} *</label>
                        <select name="content_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="both" {{ old('content_type', $site->content_type) === 'both' ? 'selected' : '' }}>{{ __('client.content_type_both') }}</option>
                            <option value="article" {{ old('content_type', $site->content_type) === 'article' ? 'selected' : '' }}>{{ __('client.content_type_article') }}</option>
                            <option value="link_insert" {{ old('content_type', $site->content_type) === 'link_insert' ? 'selected' : '' }}>{{ __('client.content_type_link') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_price') }}</label>
                        <input type="number" name="price" value="{{ old('price', $site->price) }}" step="0.01"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_description') }}</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('description', $site->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_contact') }}</label>
                    <input type="text" name="contact" value="{{ old('contact', $site->contact) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('client.site_visibility') }} *</label>
                    <select name="visibility" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="public" {{ old('visibility', $site->visibility) === 'public' ? 'selected' : '' }}>{{ __('client.visibility_public_opt') }}</option>
                        <option value="private" {{ old('visibility', $site->visibility) === 'private' ? 'selected' : '' }}>{{ __('client.visibility_private_opt') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-700">{{ __('client.save') }}</button>
                <a href="{{ route('webmaster.sites.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('client.cancel') }}</a>
            </div>
        
<script>
function updatePlatformFields() {
    var el = document.getElementById('platform_type'); var t = el ? el.value : '{{ $site->platform_type ?? 'website' }}';
    var isSocial = t !== 'website';
    document.getElementById('field_platform_url').classList.toggle('hidden', !isSocial);
    document.getElementById('field_followers').classList.toggle('hidden', !isSocial);
    document.getElementById('field_domain').classList.toggle('hidden', isSocial);
}
</script>
</form>
    </div>
</div>
</div>{{-- /tab-main --}}

{{-- Tab: Link Block --}}
<div id="tab-link-block" class="{{ $activeTab !== 'link-block' ? 'hidden' : '' }}">
@php
$bs = $site->link_block_settings ?? [];
$bsd = \App\Http\Controllers\Api\PhpClientController::defaultSettings($bs);
@endphp

@if(session('success_block'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-lg px-4 py-3 mb-4">
    {{ session('success_block') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

{{-- Left: Settings form --}}
<div>
<form method="POST" action="{{ route('webmaster.sites.link-block.update', $site) }}" id="lb-form">
@csrf
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 space-y-4">

    {{-- Display mode --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('client.lb_display_mode') }}</label>
        @php $raw = $bsd['display_mode'] ?? 'plain'; $dm = in_array($raw, ['plain','block']) ? $raw : (in_array($raw, ['block_only','mixed']) ? 'block' : 'plain'); @endphp
        <div class="space-y-1">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="radio" name="display_mode" value="plain" class="lb-trigger" <?php echo $dm === 'plain' ? 'checked' : ''; ?>>
                {{ __('client.lb_mode_plain') }}
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="radio" name="display_mode" value="block" class="lb-trigger" <?php echo $dm === 'block' ? 'checked' : ''; ?>>
                {{ __('client.lb_mode_block') }}
            </label>
        </div>
    </div>

    {{-- Delimiter --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_delimiter') }}</label>
        <input type="text" name="delimiter" value="{{ $bsd['delimiter'] }}" maxlength="50"
            class="lb-trigger w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
    </div>

    {{-- Orientation --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('client.lb_orientation') }}</label>
        <div class="flex gap-4">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="radio" name="orientation" value="horizontal" class="lb-trigger" {{ $bsd['orientation']==='horizontal' ? 'checked' : '' }}>
                {{ __('client.lb_horizontal') }}
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="radio" name="orientation" value="vertical" class="lb-trigger" {{ $bsd['orientation']==='vertical' ? 'checked' : '' }}>
                {{ __('client.lb_vertical') }}
            </label>
        </div>
    </div>

    {{-- Show elements --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('client.lb_elements') }}</label>
        <div class="flex gap-4">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="checkbox" name="show_header" value="1" class="lb-trigger rounded" {{ $bsd['show_header'] ? 'checked' : '' }}>
                {{ __('client.lb_show_header') }}
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input type="checkbox" name="show_url" value="1" class="lb-trigger rounded" {{ $bsd['show_url'] ? 'checked' : '' }}>
                {{ __('client.lb_show_url') }}
            </label>
        </div>
    </div>

    {{-- Text align --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_text_align') }}</label>
        <select name="text_align" class="lb-trigger w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="left"   {{ $bsd['text_align']==='left'   ? 'selected' : '' }}>{{ __('client.lb_align_left') }}</option>
            <option value="center" {{ $bsd['text_align']==='center' ? 'selected' : '' }}>{{ __('client.lb_align_center') }}</option>
            <option value="right"  {{ $bsd['text_align']==='right'  ? 'selected' : '' }}>{{ __('client.lb_align_right') }}</option>
        </select>
    </div>

    {{-- Sign text --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_sign_text') }}</label>
        <input type="text" name="sign_text" value="{{ $bsd['sign_text'] }}" maxlength="1000"
            class="lb-trigger w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
    </div>

    {{-- Block width --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_block_width') }}</label>
        <input type="text" name="block_width" value="{{ $bsd['block_width'] }}" maxlength="10" placeholder="auto"
            class="lb-trigger w-32 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <span class="text-xs text-gray-400 ml-2">px / %</span>
    </div>

    {{-- Font family --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_font_family') }}</label>
        <select name="font_family" class="lb-trigger w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            @foreach([
                'Verdana,sans-serif'           => 'Verdana',
                'Arial,sans-serif'             => 'Arial',
                'Georgia,serif'                => 'Georgia',
                'Tahoma,sans-serif'            => 'Tahoma',
                'Trebuchet MS,sans-serif'      => 'Trebuchet MS',
                'Times New Roman,serif'        => 'Times New Roman',
                'Courier New,monospace'        => 'Courier New',
                'system-ui,sans-serif'         => 'System UI',
            ] as $val => $label)
            <option value="{{ $val }}" {{ ($bsd['font_family'] ?? 'Verdana,sans-serif') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- CSS prefix --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('client.lb_css_prefix') }}</label>
        <input type="text" name="css_prefix" value="{{ $bsd['css_prefix'] }}" maxlength="50"
            class="lb-trigger w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
    </div>

    {{-- Colors & sizes --}}
    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ __('client.lb_colors') }}</label>
        <div class="space-y-3">
            @foreach([
                ['header_color', 'header_size', 'header_decoration', 'lb_header'],
                ['text_color',   'text_size',   null,                 'lb_text'],
                ['url_color',    'url_size',    null,                 'lb_url'],
            ] as [$col, $sz, $dec, $lbl])
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400 w-16">{{ __('client.'.$lbl) }}</span>
                <input type="color" name="{{ $col }}" value="{{ $bsd[$col] }}"
                    class="lb-trigger w-8 h-8 rounded cursor-pointer border border-gray-300 p-0.5">
                <input type="number" name="{{ $sz }}" value="{{ $bsd[$sz] }}" min="8" max="32"
                    class="lb-trigger w-16 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-2 py-1 text-sm">
                <span class="text-xs text-gray-400">px</span>
                @if($dec)
                <select name="{{ $dec }}" class="lb-trigger border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-2 py-1 text-xs">
                    <option value="none"      {{ $bsd[$dec]==='none'      ? 'selected' : '' }}>{{ __('client.lb_no_decoration') }}</option>
                    <option value="underline" {{ $bsd[$dec]==='underline' ? 'selected' : '' }}>{{ __('client.lb_underline') }}</option>
                </select>
                @endif
            </div>
            @endforeach

            {{-- Background --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400 w-16">{{ __('client.lb_bg') }}</span>
                <input type="color" name="bg_color" value="{{ $bsd['bg_color'] }}"
                    class="lb-trigger w-8 h-8 rounded cursor-pointer border border-gray-300 p-0.5">
            </div>

            {{-- Border --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400 w-16">{{ __('client.lb_border') }}</span>
                <input type="color" name="border_color" value="{{ $bsd['border_color'] }}"
                    class="lb-trigger w-8 h-8 rounded cursor-pointer border border-gray-300 p-0.5">
                <input type="number" name="border_width" value="{{ $bsd['border_width'] }}" min="0" max="10"
                    class="lb-trigger w-16 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-2 py-1 text-sm">
                <span class="text-xs text-gray-400">px</span>
                <label class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
                    <input type="checkbox" name="border_radius" value="1" class="lb-trigger rounded" {{ $bsd['border_radius'] ? 'checked' : '' }}>
                    {{ __('client.lb_rounded') }}
                </label>
            </div>
        </div>
    </div>

    <div class="pt-2">
        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
            {{ __('client.save') }}
        </button>
    </div>

</div>
</form>
</div>

{{-- Right: Live preview --}}
<div>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ __('client.lb_preview') }}</p>
        <div id="lb-preview-wrap" class="overflow-x-auto">
            <div id="lb-preview"></div>
        </div>
    </div>

    {{-- API info --}}
    <div class="mt-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('client.lb_api_endpoint') }}</p>
        <code class="text-xs font-mono text-gray-700 dark:text-gray-300 break-all">{{ url('/api/snippet/block-settings') }}?token=YOUR_TOKEN</code>
        <p class="text-xs text-gray-400 mt-2">{{ __('client.lb_api_hint') }}</p>
    </div>
</div>

</div>{{-- /grid --}}
</form>{{-- dummy close balance --}}

<script>
(function() {
    // Sample links for preview
    var sampleLinks = [
        { anchor: '{{ __("client.lb_sample_anchor1") }}', target_url: 'https://example.com', domain: 'example.com',
          text: '{{ __("client.lb_sample_text1") }}' },
        { anchor: '{{ __("client.lb_sample_anchor2") }}', target_url: 'https://shop.example.com', domain: 'shop.example.com',
          text: '{{ __("client.lb_sample_text2") }}' },
        { anchor: '{{ __("client.lb_sample_anchor3") }}', target_url: 'https://seo.example.com', domain: 'seo.example.com',
          text: '{{ __("client.lb_sample_text3") }}' },
    ];

    function getVal(name) {
        var el = document.querySelector('[name="'+name+'"]');
        if (!el) return null;
        if (el.type === 'checkbox') return el.checked;
        if (el.type === 'radio') {
            var r = document.querySelector('[name="'+name+'"]:checked');
            return r ? r.value : null;
        }
        return el.value;
    }

    function buildCSS(p, s) {
        var bg = s.bg_color || '#ffffff';
        var bc = s.border_color || '#dddddd';
        var bw = parseInt(s.border_width) || 1;
        var br = s.border_radius ? '8px' : '0';
        return [
            '.'+p+' { font-family:'+(s.font_family||'Verdana,sans-serif')+'; font-size:11px; background:'+bg+'; border:'+bw+'px solid '+bc+';',
            '  border-radius:'+br+'; padding:8px; display:block; box-sizing:border-box; }',
            '.'+p+' * { box-sizing:border-box; }',
            '.'+p+'_row { display:flex; flex-wrap:wrap; }',
            '.'+p+'_cell { padding:6px 8px; vertical-align:top; flex:1; }',
            '.'+p+'_header, .'+p+'_header a { color:'+(s.header_color||'#000066')+'; font-size:'+(s.header_size||13)+'px;',
            '  font-weight:bold; text-decoration:'+(s.header_decoration||'underline')+'; display:block; margin-bottom:3px; }',
            '.'+p+'_text, .'+p+'_text a { color:'+(s.text_color||'#000000')+'; font-size:'+(s.text_size||11)+'px;',
            '  text-decoration:none; display:block; margin-bottom:2px; }',
            '.'+p+'_url { color:'+(s.url_color||'#006600')+'; font-size:'+(s.url_size||11)+'px; display:block; }',
            '.'+p+'_sign { color:#999; font-size:10px; display:block; margin-top:4px; }',
        ].join('\n');
    }

    function buildPreview() {
        var p = getVal('css_prefix') || 'slp_block';
        var s = {
            display_mode:      getVal('display_mode'),
            delimiter:         getVal('delimiter'),
            orientation:       getVal('orientation'),
            show_header:       getVal('show_header'),
            show_url:          getVal('show_url'),
            sign_text:         getVal('sign_text'),
            font_family:       getVal('font_family'),
            block_width:       getVal('block_width'),
            text_align:        getVal('text_align'),
            header_color:      getVal('header_color'),
            header_size:       getVal('header_size'),
            header_decoration: getVal('header_decoration'),
            text_color:        getVal('text_color'),
            text_size:         getVal('text_size'),
            url_color:         getVal('url_color'),
            url_size:          getVal('url_size'),
            bg_color:          getVal('bg_color'),
            border_color:      getVal('border_color'),
            border_width:      getVal('border_width'),
            border_radius:     getVal('border_radius'),
            css_prefix:        p,
        };

        var css = buildCSS(p, s);

        // Build block HTML
        var w = s.block_width ? s.block_width + (s.block_width.indexOf('%') === -1 && s.block_width.indexOf('px') === -1 ? 'px' : '') : '100%';
        var align = s.text_align || 'left';
        var html = '<style>' + css + '</style>';
        html += '<div class="'+p+'" style="width:'+w+';text-align:'+align+';">';

        if (s.orientation === 'vertical') {
            sampleLinks.forEach(function(l) {
                html += '<div class="'+p+'_cell">';
                if (s.show_header) html += '<span class="'+p+'_header"><a href="'+l.target_url+'">'+l.anchor+'</a></span>';
                html += '<span class="'+p+'_text">'+l.text+'</span>';
                if (s.show_url) html += '<span class="'+p+'_url">'+l.domain+'</span>';
                html += '</div>';
            });
        } else {
            html += '<div class="'+p+'_row">';
            sampleLinks.forEach(function(l) {
                html += '<div class="'+p+'_cell">';
                if (s.show_header) html += '<span class="'+p+'_header"><a href="'+l.target_url+'">'+l.anchor+'</a></span>';
                html += '<span class="'+p+'_text">'+l.text+'</span>';
                if (s.show_url) html += '<span class="'+p+'_url">'+l.domain+'</span>';
                html += '</div>';
            });
            html += '</div>';
        }

        if (s.sign_text) html += '<span class="'+p+'_sign">'+s.sign_text+'</span>';
        html += '</div>';

        // Plain mode preview
        var delimiter = s.delimiter || ' | ';
        var parts = [];
        sampleLinks.forEach(function(l) {
            parts.push('<a href="'+l.target_url+'" style="color:inherit;text-decoration:underline;">'+l.anchor+'</a>');
        });
        var plainHtml = '<p style="font-family:'+(s.font_family||'Arial,sans-serif')+';font-size:13px;line-height:2;">'
            + parts.join(delimiter) + '</p>';

        var mode = s.display_mode || 'plain';
        if (mode === 'plain') {
            document.getElementById('lb-preview').innerHTML = plainHtml;
        } else if (mode === 'mixed') {
            document.getElementById('lb-preview').innerHTML = plainHtml + html;
        } else {
            document.getElementById('lb-preview').innerHTML = html;
        }
    }

    // Tab switching
    window.switchTab = function(tab) {
        document.getElementById('tab-main').classList.toggle('hidden', tab !== 'main');
        document.getElementById('tab-link-block').classList.toggle('hidden', tab !== 'link-block');
        document.getElementById('tab-danger').classList.toggle('hidden', tab !== 'danger');
        ['main','link-block','danger'].forEach(function(t) {
            var btn = document.getElementById('tab-btn-'+t);
            if (t === tab) {
                btn.classList.add('border-yellow-500','text-yellow-600');
                btn.classList.remove('border-transparent','text-gray-500');
            } else {
                btn.classList.remove('border-yellow-500','text-yellow-600');
                btn.classList.add('border-transparent','text-gray-500');
            }
        });
    };

    // Live update on any input change
    document.querySelectorAll('.lb-trigger').forEach(function(el) {
        el.addEventListener('input', buildPreview);
        el.addEventListener('change', buildPreview);
    });

    // Initial render
    buildPreview();

    // Auto-switch to link-block tab if session said so
    @if($activeTab === 'link-block')
    switchTab('link-block');
    @endif
})();
</script>

</div>{{-- /tab-link-block --}}


{{-- Tab: Danger Zone --}}
<div id="tab-danger" class="hidden">
    <div class="max-w-2xl">
        <div class="border border-red-200 dark:border-red-800 rounded-xl">
            <div class="bg-red-50 dark:bg-red-900/20 px-5 py-4 border-b border-red-200 dark:border-red-800">
                <h2 class="text-base font-semibold text-red-700 dark:text-red-400">{{ __('client.danger_zone_title') }}</h2>
            </div>
            <div class="bg-white dark:bg-gray-900 px-5 pt-5 pb-8">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{{ __('client.danger_zone_desc') }}</p>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3 mb-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.site_domain') }}</p>
                    <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $site->domain }}</p>
                </div>

                <form method="POST" action="{{ route('webmaster.sites.destroy', $site) }}" id="delete-site-form">
                    @csrf @method('DELETE')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('client.danger_zone_confirm_label') }}
                        </label>
                        <input type="text" id="delete-confirm-input"
                            placeholder="{{ $site->domain }}"
                            autocomplete="off"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                        <p id="delete-mismatch-msg" class="text-xs text-red-500 mt-1 hidden">{{ __('client.danger_zone_mismatch') }}</p>
                    </div>
                    <button type="button"
                        onclick="openDeleteModal('{{ $site->domain }}')"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                        {{ __('client.danger_zone_btn') }}
                    </button>
                </form>
                <div class="h-4"></div>
            </div>
        </div>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    {{-- Dialog --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full border border-red-200 dark:border-red-800 overflow-hidden">
        {{-- Red top bar --}}
        <div class="h-1.5 bg-red-600 w-full"></div>
        <div class="px-6 py-5">
            {{-- Icon + title --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('client.danger_zone_title') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="modal-domain-display"></p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{{ __('client.danger_zone_modal_text') }}</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    {{ __('client.cancel') }}
                </button>
                <button onclick="submitDeleteSite()"
                    class="flex-1 px-4 py-2 rounded-lg text-sm font-medium bg-red-600 hover:bg-red-700 text-white transition-colors">
                    {{ __('client.danger_zone_btn') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(expectedDomain) {
    var input = document.getElementById('delete-confirm-input');
    var msg   = document.getElementById('delete-mismatch-msg');
    if (input.value.trim() !== expectedDomain) {
        msg.classList.remove('hidden');
        input.focus();
        return;
    }
    msg.classList.add('hidden');
    document.getElementById('modal-domain-display').textContent = expectedDomain;
    var modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    var modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitDeleteSite() {
    document.getElementById('delete-site-form').submit();
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

@endsection
