@extends('webmaster.layouts.app')
@section('title', __('client.integrations_title'))

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    {{-- WordPress Plugin --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-4">
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 1.542c2.282 0 4.368.831 5.976 2.2L4.742 17.976A8.432 8.432 0 013.542 12c0-4.669 3.789-8.458 8.458-8.458zm0 16.916a8.414 8.414 0 01-5.976-2.2l13.234-14.234A8.414 8.414 0 0120.458 12c0 4.669-3.789 8.458-8.458 8.458z"/>
                </svg>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">WordPress Plugin</h2>
                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">v{{ $pluginVersion }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3">{{ __('client.integrations_wp_desc') }}</p>
            <a href="{{ $pluginUrl }}" download
               class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ __('client.plugin_download') }}
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 text-sm text-gray-600 dark:text-gray-300">
            <p class="font-medium text-gray-900 dark:text-white mb-2">{{ __('client.plugin_how_install') }}</p>
            <ol class="list-decimal list-inside space-y-1.5">
                <li>{!! __('client.plugin_step1') !!}</li>
                <li>{!! __('client.plugin_step2') !!}</li>
                <li>{!! __('client.plugin_step3') !!}</li>
                <li>{!! __('client.plugin_step4') !!}</li>
                <li>{!! __('client.plugin_step5') !!}</li>
            </ol>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.plugin_token_hint') }}</p>
            <div class="space-y-2">
                @foreach(\App\Helpers\AuthHelper::webmaster()->sites as $site)
                @php $token = $site->tenantTokens()->where('status', 'active')->first(); @endphp
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3">
                    <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ $site->domain }}</span>
                    @if($token)
                    <code class="block text-xs bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-2 py-1.5 text-gray-600 dark:text-gray-300 font-mono select-all break-all">{{ $token->token }}</code>
                    @else
                    <a href="{{ route('webmaster.sites.tokens.index', $site) }}" class="text-xs text-blue-600 hover:underline">{{ __('client.plugin_create_token') }}</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.plugin_requirements') }}</p>
            <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1 list-disc list-inside">
                <li>WordPress 5.0+</li>
                <li>PHP 7.4+</li>
                <li>{{ __('client.plugin_req3') }}</li>
            </ul>
        </div>
    </div>

    {{-- PHP Client --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-4">
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">PHP {{ __('client.php_client_title') }}</h2>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3">{{ __('client.php_client_desc') }}</p>
            <a href="{{ route('webmaster.plugin.select') }}"
               class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ __('client.php_client_download') }}
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 text-sm text-gray-600 dark:text-gray-300">
            <p class="font-medium text-gray-900 dark:text-white mb-2">{{ __('client.php_client_how_install') }}</p>
            <ol class="list-decimal list-inside space-y-1.5">
                <li>{{ __('client.php_client_step1') }}</li>
                <li>{!! __('client.php_client_step2') !!}</li>
                <li>{!! __('client.php_client_step3') !!}</li>
                <li>{{ __('client.php_client_step4') }}</li>
            </ol>
        </div>

        {{-- Code example --}}
        <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-4 mb-4 overflow-x-auto">
            <pre style="color:#4ade80;font-size:.75rem;font-family:monospace;white-space:pre">&lt;?php
define('SEOLINKPLACE_TOKEN', 'YOUR_TOKEN_HERE');
require_once 'seolinkplace-client.php';
$sh = new SeoHands();
?&gt;

&lt;!-- {{ __('client.php_client_example_links') }} --&gt;
&lt;?= $sh-&gt;return_links(3) ?&gt;

&lt;!-- {{ __('client.php_client_example_block') }} --&gt;
&lt;?= $sh-&gt;return_block_links(5) ?&gt;</pre>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.plugin_token_hint') }}</p>
            <div class="space-y-2">
                @foreach(\App\Helpers\AuthHelper::webmaster()->sites as $site)
                @php $token = $site->tenantTokens()->where('status', 'active')->first(); @endphp
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3">
                    <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ $site->domain }}</span>
                    @if($token)
                    <code class="block text-xs bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-2 py-1.5 text-gray-600 dark:text-gray-300 font-mono select-all break-all">{{ $token->token }}</code>
                    @else
                    <a href="{{ route('webmaster.sites.tokens.index', $site) }}" class="text-xs text-blue-600 hover:underline">{{ __('client.plugin_create_token') }}</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- More coming --}}
    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-sm text-gray-500 dark:text-gray-400 text-center">
        {{ __('client.integrations_more') }}
    </div>

</div>
@endsection
