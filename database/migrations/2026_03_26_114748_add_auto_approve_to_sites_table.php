<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Дозвіл автопублікації статей для конкретного клієнта на конкретному сайті
        Schema::create('site_client_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->boolean('auto_approve_articles')->default(false);
            $table->timestamps();
            $table->unique(['site_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_client_permissions');
    }
};
