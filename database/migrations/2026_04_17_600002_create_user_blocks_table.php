<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blocker_id')
                ->comment('unified_users.id — хто ініціював (адмін або система)');
            $table->unsignedBigInteger('blocked_id')
                ->comment('unified_users.id — кого заблоковано від взаємодії');
            $table->unsignedBigInteger('complaint_id')->nullable()
                ->comment('task_complaints.id — причина блоку');
            $table->timestamps();

            // Один блок між двома користувачами
            $table->unique(['blocker_id', 'blocked_id']);
            $table->index('blocked_id');
            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_blocks');
    }
};
