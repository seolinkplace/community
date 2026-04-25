<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->decimal('price_link_per_month', 10, 2)->nullable()->after('price_link_per_day');
            $table->decimal('price_onclick_per_month', 10, 2)->nullable()->after('price_onclick_per_day');
            $table->decimal('price_article_per_month', 10, 2)->nullable()->after('price_article_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->dropColumn(['price_link_per_month', 'price_onclick_per_month', 'price_article_per_month']);
        });
    }
};
