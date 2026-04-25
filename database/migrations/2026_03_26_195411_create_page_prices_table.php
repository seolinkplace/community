<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_page_id')->nullable()->constrained()->nullOnDelete();
            // null wp_page_id = дефолтна ціна для всього сайту
            $table->decimal('base_price_per_day', 10, 2)->nullable();
            // Окремі ціни по типах (якщо null — використовується base_price * коефіцієнт)
            $table->decimal('price_link_per_day', 10, 2)->nullable();
            $table->decimal('price_onclick_per_day', 10, 2)->nullable();
            $table->decimal('price_article_once', 10, 2)->nullable();
            $table->decimal('price_article_per_day', 10, 2)->nullable();
            // Коефіцієнти відносно base_price
            $table->decimal('coef_link', 5, 2)->default(1.00);
            $table->decimal('coef_onclick', 5, 2)->default(1.20);
            $table->decimal('coef_article_once', 5, 2)->default(5.00);
            $table->decimal('coef_article_daily', 5, 2)->default(2.00);
            $table->unsignedTinyInteger('max_placements')->default(5);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->unique(['site_id', 'site_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_prices');
    }
};
