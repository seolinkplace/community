<?php

use Illuminate\Support\Facades\Schedule;

// Агрегація статистики щоночі о 3:00
Schedule::command('stats:aggregate')->dailyAt('03:00');

// Щоденна синхронізація WP сайтів
// Schedule::command('wp:sync-all')->dailyAt('06:00'); // TODO: команда не існує

// Щоденне списання за активні campaign_links
Schedule::command('campaigns:charge-daily')->dailyAt('00:01');
// Погодинне списання за onclick кліки
Schedule::command('campaigns:charge-onclick')->hourly();

// Щоденна перевірка всіх активних campaign_links парсером
Schedule::call(function () {
    app(\App\Services\ParserService::class)->queueAllActiveLinks();
})->dailyAt('02:00')->name('parser:queue-active-links');
// Перевірка прямих USDT платежів кожні 3 хвилини
Schedule::command('payments:check-usdt')->everyThreeMinutes();

// Weekly whois sync — 50 sites per run, oldest-synced first
Schedule::command('whois:sync')->dailyAt('04:00');
// Auto-approve task completions every 30 minutes
Schedule::command('tasks:auto-approve')->everyThirtyMinutes();
// Release expired task claims every 5 minutes
Schedule::command('tasks:release-claims')->everyFiveMinutes();

// Monthly subscription charging + grace period expiry
Schedule::command('subscriptions:charge')->dailyAt('00:30');
// Протухання pending direct payments щогодини
Schedule::command('direct-payments:expire')->hourly();
