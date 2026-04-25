<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('site_connections')->cascadeOnDelete();
            $table->string('url');
            $table->string('title')->nullable();
            $table->json('anchors')->nullable();
            $table->unsignedInteger('wp_post_id')->nullable();
            $table->enum('post_type', ['post', 'page', 'custom'])->default('post');
            $table->enum('status', ['publish', 'draft', 'private'])->default('publish');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_pages');
    }
};
