<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->boolean('is_adult')->default(false)->after('visibility');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_adult_verified')->default(false)->after('email');
            $table->date('birth_date')->nullable()->after('is_adult_verified');
        });
    }
    public function down(): void
    {
        Schema::table('sites', fn($t) => $t->dropColumn('is_adult'));
        Schema::table('clients', fn($t) => $t->dropColumn(['is_adult_verified', 'birth_date']));
    }
};
