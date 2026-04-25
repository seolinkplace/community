<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->timestamp('banned_until')->nullable()->after('is_trusted')
                ->comment('null = not banned, specific date = temp ban, 9999-01-01 = permanent');
            $table->text('ban_reason')->nullable()->after('banned_until');
            $table->unsignedSmallInteger('warning_count')->default(0)->after('ban_reason');
        });
    }

    public function down(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->dropColumn(['banned_until', 'ban_reason', 'warning_count']);
        });
    }
};
