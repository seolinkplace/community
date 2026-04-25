<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_token_id')->constrained('tenant_tokens')->cascadeOnDelete();
            $table->foreignId('webmaster_id')->constrained('webmasters')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('wp_url');
            $table->string('wp_username')->nullable();
            $table->text('wp_app_password')->nullable();
            $table->enum('status', ['active', 'error', 'disconnected'])->default('active');
            $table->string('wp_version')->nullable();
            $table->unsignedInteger('pages_count')->default(0);
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_connections');
    }
};
