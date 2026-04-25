<?php

use Illuminate\Support\Facades\Route;


// Track API
Route::middleware('throttle:track')->prefix('track')->name('track.')->group(function () {
    Route::get('view',         [\Modules\Parser\Http\Controllers\Api\TrackController::class, 'view'])->name('view');
    Route::get('click',        [\Modules\Parser\Http\Controllers\Api\TrackController::class, 'click'])->name('click');
    Route::get('anchor-click', [\Modules\Parser\Http\Controllers\Api\TrackController::class, 'anchorClick'])->name('anchor-click');
});

// WordPress Plugin API
Route::middleware('throttle:wp-api')->prefix('wp')->name('wp.')->group(function () {
    Route::post('connect',  [\Modules\Parser\Http\Controllers\Api\WpController::class, 'connect'])->name('connect');
    Route::post('pages',    [\Modules\Parser\Http\Controllers\Api\WpController::class, 'syncPages'])->name('pages');
    Route::get('articles',  [\Modules\Parser\Http\Controllers\Api\WpController::class, 'getArticles'])->name('articles');
    Route::post('report',   [\Modules\Parser\Http\Controllers\Api\WpController::class, 'report'])->name('report');
    Route::get('orders',    [\Modules\Parser\Http\Controllers\Api\WpController::class, 'getOrders'])->name('orders');
    Route::get('stats',     [\Modules\Parser\Http\Controllers\Api\WpController::class, 'stats'])->name('stats');
    Route::get('page-links', [\Modules\Parser\Http\Controllers\Api\WpController::class, 'pageLinks'])->name('page-links');
    Route::get('onclick-rules', [\Modules\Parser\Http\Controllers\Api\WpController::class, 'onclickRules'])->name('onclick-rules');
    Route::post('article-deleted', [\Modules\Parser\Http\Controllers\Api\WpController::class, 'articleDeleted'])->name('article-deleted');
    Route::post('orders/{id}/placed', [\Modules\Parser\Http\Controllers\Api\WpController::class, 'orderPlaced'])->name('orders.placed');
    Route::post('sync',     function (\Illuminate\Http\Request $request) {
        $token = $request->header('X-Seohands-Token') ?? $request->query('token');
        $tenantToken = \App\Models\TenantToken::where('token', $token)
            ->where('status', 'active')
            ->where('wp_enabled', true)
            ->first();

        if (!$tenantToken) return response()->json(['error' => 'Invalid token'], 401);

        $wpSite = \App\Models\WpSite::where('tenant_token_id', $tenantToken->id)->first();
        if (!$wpSite) return response()->json(['error' => 'WP site not connected'], 404);

        \App\Jobs\SyncWpPages::dispatch($wpSite);
        return response()->json(['ok' => true, 'message' => 'Sync queued']);
    })->name('sync');
});

// Parser API (internal — Go service)
Route::prefix('parser')->name('parser.')->group(function () {
    Route::post('result', [\Modules\Parser\Http\Controllers\Api\ParserController::class, 'result'])->name('result');
    Route::get('job',    [\Modules\Parser\Http\Controllers\Api\ParserController::class, 'nextJob'])->name('job');
});

// PHP Client API (non-WP sites)
Route::middleware('throttle:snippet')->prefix('snippet')->name('snippet.')->group(function () {
    Route::get('block-settings', [\App\Http\Controllers\Api\PhpClientController::class, 'blockSettings'])->name('block-settings');
    Route::get('render', [\App\Http\Controllers\Api\PhpClientController::class, 'render'])->name('render');
    Route::get('links', [\App\Http\Controllers\Api\PhpClientController::class, 'links'])->name('links');
    Route::get('pageview', [\App\Http\Controllers\Api\PhpClientController::class, 'pageview'])->name('pageview');
});
