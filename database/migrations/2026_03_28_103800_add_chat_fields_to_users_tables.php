<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->timestamp('chat_banned_at')->nullable()->after('status');
        });

        Schema::table('webmasters', function (Blueprint $table) {
            $table->timestamp('chat_banned_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('chat_banned_at');
        });
        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn('chat_banned_at');
        });
    }
};
