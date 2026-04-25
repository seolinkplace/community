<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('site_pages', 'site_pages');

        // FK в site_connections теж може бути — перевіримо і виправимо
        if (Schema::hasColumn('page_prices', 'site_page_id')) {
            Schema::table('page_prices', function (Blueprint $table) {
                $table->renameColumn('site_page_id', 'site_page_id');
            });
        }

        // wp_site_id в site_pages -> site_id
        if (Schema::hasColumn('site_pages', 'site_id')) {
            Schema::table('site_pages', function (Blueprint $table) {
                $table->renameColumn('site_id', 'site_id');
            });
        }

        // price_type на page_prices
        Schema::table('page_prices', function (Blueprint $table) {
            $table->enum('price_type', ['link', 'onclick', 'article_client', 'article_webmaster'])
                  ->default('link')->after('scope_type');
        });
    }

    public function down(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->dropColumn('price_type');
        });
        if (Schema::hasColumn('site_pages', 'site_id')) {
            Schema::table('site_pages', fn($t) => $t->renameColumn('site_id', 'site_id'));
        }
        if (Schema::hasColumn('page_prices', 'site_page_id')) {
            Schema::table('page_prices', fn($t) => $t->renameColumn('site_page_id', 'site_page_id'));
        }
        Schema::rename('site_pages', 'site_pages');
    }
};
