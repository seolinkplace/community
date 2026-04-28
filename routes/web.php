<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth('unified')->check()) {
        return redirect()->route('unified.dashboard');
    }
    return view('welcome');
});

// ─── Client Cabinet ───────────────────────────────────────────────────────────
Route::prefix('app')->name('client.')->group(function () {
    Route::middleware(['any_client', 'verified'])->group(function () {
        Route::post('logout',   [\Modules\Auth\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\Modules\Core\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile',          [\Modules\Core\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile',          [\Modules\Core\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [\Modules\Core\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('profile.password');

        // Catalog
        Route::get('catalog',               [\Modules\Core\Http\Controllers\Client\CatalogController::class, 'index'])->name('catalog.index');
        Route::get('catalog/{site}',        [\Modules\Core\Http\Controllers\Client\CatalogController::class, 'show'])->name('catalog.show');

        // Support
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/',           [\Modules\Support\Http\Controllers\SupportTicketController::class, 'index'])->name('index');
            Route::get('create',      [\Modules\Support\Http\Controllers\SupportTicketController::class, 'create'])->name('create');
            Route::post('/',          [\Modules\Support\Http\Controllers\SupportTicketController::class, 'store'])->name('store');
            Route::get('{id}',        [\Modules\Support\Http\Controllers\SupportTicketController::class, 'show'])->name('show');
            Route::post('{id}/reply', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'reply'])->name('reply');
            Route::post('{id}/close', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'close'])->name('close');
        });
    });
});

// ─── Webmaster Cabinet ────────────────────────────────────────────────────────
Route::prefix('wm')->name('webmaster.')->group(function () {
    Route::middleware(['any_webmaster', 'verified'])->group(function () {
        Route::post('logout',   [\Modules\Auth\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\Modules\Core\Http\Controllers\Webmaster\DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile',          [\Modules\Core\Http\Controllers\Webmaster\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile',          [\Modules\Core\Http\Controllers\Webmaster\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [\Modules\Core\Http\Controllers\Webmaster\ProfileController::class, 'updatePassword'])->name('profile.password');

        // Sites
        Route::resource('sites', \Modules\Sites\Http\Controllers\Webmaster\SiteController::class);
        Route::post('sites/{site}/verify',           [\Modules\Sites\Http\Controllers\Webmaster\SiteVerificationController::class, 'verify'])->name('sites.verify');
        Route::post('sites/{site}/regenerate-token', [\Modules\Sites\Http\Controllers\Webmaster\SiteVerificationController::class, 'regenerateToken'])->name('sites.regenerate-token');

        // Support
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/',           [\Modules\Support\Http\Controllers\SupportTicketController::class, 'index'])->name('index');
            Route::get('create',      [\Modules\Support\Http\Controllers\SupportTicketController::class, 'create'])->name('create');
            Route::post('/',          [\Modules\Support\Http\Controllers\SupportTicketController::class, 'store'])->name('store');
            Route::get('{id}',        [\Modules\Support\Http\Controllers\SupportTicketController::class, 'show'])->name('show');
            Route::post('{id}/reply', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'reply'])->name('reply');
            Route::post('{id}/close', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'close'])->name('close');
        });
    });
});

// ─── Unified Auth ─────────────────────────────────────────────────────────────
Route::prefix('u')->name('unified.')->group(function () {
    Route::middleware('guest:unified')->group(function () {
        Route::get('login',     [\Modules\Auth\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
        Route::post('login',    [\Modules\Auth\Http\Controllers\AuthController::class, 'login']);
        Route::get('register',  [\Modules\Auth\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [\Modules\Auth\Http\Controllers\AuthController::class, 'register']);
    });

    Route::middleware(['unified', 'check.banned', 'verified'])->group(function () {
        Route::post('add-role',     [\Modules\Auth\Http\Controllers\AddRoleController::class, 'store'])->name('add.role');
        Route::get('google/select-role',  [\Modules\Core\Http\Controllers\Unified\GoogleRoleController::class, 'show'])->name('google.role.select');
        Route::post('google/select-role', [\Modules\Core\Http\Controllers\Unified\GoogleRoleController::class, 'store'])->name('google.role.store');
        Route::post('logout',       [\Modules\Auth\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        Route::get('export-data',   [\Modules\Core\Http\Controllers\Unified\ProfileController::class, 'exportData'])->name('export.data');
        Route::get('/',             [\Modules\Core\Http\Controllers\Unified\DashboardController::class, 'index'])->name('dashboard');
        Route::delete('account',    [\Modules\Core\Http\Controllers\Unified\ProfileController::class, 'deleteAccount'])->name('account.delete');

        // Email verification
        Route::get('email/verify', [\Modules\Auth\Http\Controllers\VerifyEmailController::class, 'notice'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [\Modules\Auth\Http\Controllers\VerifyEmailController::class, 'verify'])->name('verification.verify')->middleware(['signed', 'throttle:6,1']);
        Route::post('email/verification-notification', [\Modules\Auth\Http\Controllers\VerifyEmailController::class, 'resend'])->name('verification.resend')->middleware('throttle:6,1');

        Route::get('banned',  [\Modules\Core\Http\Controllers\Unified\UserAppealController::class, 'banned'])->name('banned')->withoutMiddleware('check.banned');
        Route::post('appeal', [\Modules\Core\Http\Controllers\Unified\UserAppealController::class, 'store'])->name('appeal.store')->withoutMiddleware('check.banned');

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/',           [\Modules\Support\Http\Controllers\SupportTicketController::class, 'index'])->name('index');
            Route::get('create',      [\Modules\Support\Http\Controllers\SupportTicketController::class, 'create'])->name('create');
            Route::post('/',          [\Modules\Support\Http\Controllers\SupportTicketController::class, 'store'])->name('store');
            Route::get('{id}',        [\Modules\Support\Http\Controllers\SupportTicketController::class, 'show'])->name('show');
            Route::post('{id}/reply', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'reply'])->name('reply');
            Route::post('{id}/close', [\Modules\Support\Http\Controllers\SupportTicketController::class, 'close'])->name('close');
        });
    });
});

// ─── Google OAuth ─────────────────────────────────────────────────────────────
Route::get('/auth/google',          [\Modules\Auth\Http\Controllers\GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\Modules\Auth\Http\Controllers\GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/link',     [\Modules\Auth\Http\Controllers\GoogleAuthController::class, 'redirectForLink'])->name('auth.google.link')->middleware('auth:unified');
Route::delete('/auth/google/unlink',[\Modules\Auth\Http\Controllers\GoogleAuthController::class, 'unlink'])->name('auth.google.unlink')->middleware('auth:unified');

// ─── Contact ──────────────────────────────────────────────────────────────────
Route::get('/contact',              [\Modules\Support\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact',             [\Modules\Support\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/sent/{token}', [\Modules\Support\Http\Controllers\ContactController::class, 'sent'])->name('contact.sent');
Route::get('/contact/reply/{token}',[\Modules\Support\Http\Controllers\ContactController::class, 'reply'])->name('contact.reply');

// ─── Language switcher ────────────────────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    $localeService = app(\App\Services\LocaleService::class);
    if ($localeService->isSupported($locale)) {
        session(['locale' => $locale]);
        $user = auth('unified')->user();
        if ($user) {
            $user->update(['locale' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

// ─── Blog ─────────────────────────────────────────────────────────────────────
Route::get('/blog',        [\Modules\Blog\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\Modules\Blog\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// ─── Rules ────────────────────────────────────────────────────────────────────
Route::get('/rules',                   [\Modules\Core\Http\Controllers\RulesController::class, 'index'])->name('rules.index');
Route::get('/rules/{rule:slug}',       [\Modules\Core\Http\Controllers\RulesController::class, 'show'])->name('rules.show');
Route::post('/rules/{rule:slug}/comments', [\Modules\Core\Http\Controllers\RulesController::class, 'storeComment'])->name('rules.comment')->middleware('auth:unified');

// ─── Apply ────────────────────────────────────────────────────────────────────
Route::post('/apply',    [\Modules\Core\Http\Controllers\ApplyController::class, 'store']);
Route::post('/en/apply', [\Modules\Core\Http\Controllers\ApplyController::class, 'store']);

// ─── Landing ──────────────────────────────────────────────────────────────────
Route::get('/en/', function() { session(['locale' => 'en']); return view('welcome'); })->name('landing.en');
Route::get('/privacy', fn() => view('privacy'))->name('privacy');

// ─── Legacy redirects ─────────────────────────────────────────────────────────
Route::get('app/login', fn() => redirect()->route('unified.login'));
Route::get('wm/login',  fn() => redirect()->route('unified.login'));
