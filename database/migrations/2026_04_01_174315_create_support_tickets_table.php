<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('unified_users')->onDelete('cascade');
            $table->string('role', 20); // client, webmaster
            $table->string('subject', 255);
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->foreignId('assigned_to')->nullable()->constrained('unified_users')->nullOnDelete();
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
