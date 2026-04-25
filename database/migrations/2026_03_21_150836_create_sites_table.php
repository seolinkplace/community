<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webmaster_id')->constrained('webmasters')->cascadeOnDelete();
            $table->string('domain');
            $table->string('niche')->nullable();
            $table->string('language', 10)->nullable();
            $table->unsignedSmallInteger('dr')->nullable();
            $table->unsignedInteger('traffic')->nullable();
            $table->enum('content_type', ['article', 'link_insert', 'both'])->default('both');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('contact')->nullable();
            $table->enum('status', ['active', 'suspended', 'rejected'])->default('active');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->string('metrics_source')->nullable();
            $table->timestamp('metrics_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['webmaster_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
