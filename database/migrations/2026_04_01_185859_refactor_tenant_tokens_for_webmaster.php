<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_tokens', function (Blueprint $table) {
            // client_id робимо nullable
            $table->foreignId('client_id')->nullable()->change();
            // додаємо webmaster_id якщо немає
            if (!Schema::hasColumn('tenant_tokens', 'webmaster_id')) {
                $table->foreignId('webmaster_id')->nullable()->after('client_id');
            }
        });

        // Заповнюємо webmaster_id з sites.webmaster_id для існуючих записів
        DB::statement('
            UPDATE tenant_tokens tt
            JOIN sites s ON s.id = tt.site_id
            SET tt.webmaster_id = s.webmaster_id
        ');
    }

    public function down(): void
    {
        Schema::table('tenant_tokens', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable(false)->change();
            if (Schema::hasColumn('tenant_tokens', 'webmaster_id')) {
                $table->dropColumn('webmaster_id');
            }
        });
    }
};
