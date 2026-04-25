<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_appeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->comment('unified_users.id — хто подає апеляцію');
            $table->enum('appeal_type', ['account_ban', 'user_block'])
                ->comment('account_ban = апеляція на бан акаунта, user_block = апеляція на взаємний бан');
            $table->unsignedBigInteger('reference_id')->nullable()
                ->comment('user_blocks.id або task_complaints.id залежно від appeal_type');
            $table->text('message');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_appeals');
    }
};
