<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // support_tickets: прискорення фільтрації за user_id + status
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'support_tickets_user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex('support_tickets_user_status_idx');
        });
    }
};
