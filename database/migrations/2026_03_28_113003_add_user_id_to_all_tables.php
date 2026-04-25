<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Таблиці де треба додати user_id замість client_id
    private array $clientTables = [
        'articles', 'campaigns', 'compensation_requests',
        'link_stats', 'link_stats_daily', 'links',
        'site_client_permissions', 'tenant_tokens', 'wallets',
    ];

    // Таблиці де треба додати user_id замість webmaster_id
    private array $webmasterTables = [
        'sites', 'webmaster_wallets', 'webmaster_withdrawals', 'site_connections',
    ];

    // Таблиці де є обидва
    private array $bothTables = [
        'orders', 'webmaster_client_settings',
    ];

    public function up(): void
    {
        // ─── Додаємо user_id колонки ─────────────────────────────────────────

        foreach ($this->clientTables as $table) {
            if (!Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('id');
                    $t->index('user_id');
                });
            }
        }

        foreach ($this->webmasterTables as $table) {
            if (!Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('id');
                    $t->index('user_id');
                });
            }
        }

        // orders і webmaster_client_settings — обидва FK
        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'client_user_id')) {
                $t->unsignedBigInteger('client_user_id')->nullable()->after('id');
                $t->unsignedBigInteger('webmaster_user_id')->nullable()->after('client_user_id');
            }
        });

        Schema::table('webmaster_client_settings', function (Blueprint $t) {
            if (!Schema::hasColumn('webmaster_client_settings', 'webmaster_user_id')) {
                $t->unsignedBigInteger('webmaster_user_id')->nullable()->after('id');
                $t->unsignedBigInteger('client_user_id')->nullable()->after('webmaster_user_id');
            }
        });

        // ─── Заповнюємо user_id через _migration_map ─────────────────────────

        // client_id → user_id
        foreach ($this->clientTables as $table) {
            DB::statement("
                UPDATE {$table} t
                JOIN _migration_map m ON m.old_type = 'client' AND m.old_id = t.client_id
                SET t.user_id = m.new_user_id
            ");
        }

        // webmaster_id → user_id
        foreach ($this->webmasterTables as $table) {
            DB::statement("
                UPDATE {$table} t
                JOIN _migration_map m ON m.old_type = 'webmaster' AND m.old_id = t.webmaster_id
                SET t.user_id = m.new_user_id
            ");
        }

        // orders
        DB::statement("
            UPDATE orders t
            JOIN _migration_map mc ON mc.old_type = 'client' AND mc.old_id = t.client_id
            SET t.client_user_id = mc.new_user_id
        ");
        DB::statement("
            UPDATE orders t
            JOIN _migration_map mw ON mw.old_type = 'webmaster' AND mw.old_id = t.webmaster_id
            SET t.webmaster_user_id = mw.new_user_id
        ");

        // webmaster_client_settings
        DB::statement("
            UPDATE webmaster_client_settings t
            JOIN _migration_map mw ON mw.old_type = 'webmaster' AND mw.old_id = t.webmaster_id
            SET t.webmaster_user_id = mw.new_user_id
        ");
        DB::statement("
            UPDATE webmaster_client_settings t
            JOIN _migration_map mc ON mc.old_type = 'client' AND mc.old_id = t.client_id
            SET t.client_user_id = mc.new_user_id
        ");

        // site_client_permissions — теж має webmaster через site
        // tasks — creator_type/performer_type (morphable, окремо)
    }

    public function down(): void
    {
        foreach ($this->clientTables as $table) {
            Schema::table($table, fn(Blueprint $t) => $t->dropColumn('user_id'));
        }
        foreach ($this->webmasterTables as $table) {
            Schema::table($table, fn(Blueprint $t) => $t->dropColumn('user_id'));
        }
        Schema::table('orders', function (Blueprint $t) {
            $t->dropColumn(['client_user_id', 'webmaster_user_id']);
        });
        Schema::table('webmaster_client_settings', function (Blueprint $t) {
            $t->dropColumn(['webmaster_user_id', 'client_user_id']);
        });
    }
};
