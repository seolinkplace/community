<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content');
            $table->longText('content_en')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_description_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
