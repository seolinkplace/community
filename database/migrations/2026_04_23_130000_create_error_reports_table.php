<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_reports', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->string('message')->index();
            $table->string('exception_class')->index();
            $table->text('file');
            $table->unsignedInteger('line');
            $table->longText('trace')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->json('input')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_type')->nullable();
            $table->enum('status', ['new', 'seen', 'resolved'])->default('new')->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_reports');
    }
};
