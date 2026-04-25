<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_rules', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_uk');
            $table->string('title_en');
            $table->longText('body_uk');
            $table->longText('body_en');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('rule_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('platform_rules')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('unified_users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('rule_comments')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_comments');
        Schema::dropIfExists('platform_rules');
    }
};
