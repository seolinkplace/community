<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webmaster_profiles', function (Blueprint $table) {
            $table->json('services')->nullable()->after('payment_details');
            // services: ["place_website", "place_social", "write", "write_and_place"]
        });
    }

    public function down(): void
    {
        Schema::table('webmaster_profiles', function (Blueprint $table) {
            $table->dropColumn('services');
        });
    }
};
