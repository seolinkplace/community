<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->string('locale', 2)->default('uk')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
