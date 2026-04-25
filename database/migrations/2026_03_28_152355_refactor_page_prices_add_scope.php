<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->enum('scope_type', ['site_default', 'depth', 'url', 'url_client'])
                  ->default('site_default')->after('site_id');
            $table->tinyInteger('scope_depth')->unsigned()->nullable()->after('scope_type');
            $table->string('scope_url', 500)->nullable()->after('scope_depth');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete()->after('scope_url');
            $table->boolean('adult_allowed')->default(false)->after('is_public');
            $table->unique(['site_id', 'scope_type', 'scope_depth', 'scope_url', 'client_id'], 'page_prices_scope_unique');
        });

        DB::statement("
            UPDATE page_prices
            SET scope_type = CASE
                WHEN site_page_id IS NULL THEN 'site_default'
                ELSE 'url'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->dropUnique('page_prices_scope_unique');
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn(['scope_type', 'scope_depth', 'scope_url', 'adult_allowed']);
        });
    }
};
