<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->integer('link_limit')->default(5);
            $table->enum('link_type', ['dofollow', 'nofollow', 'mixed'])->default('dofollow');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_tokens');
    }
};
