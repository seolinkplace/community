<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('webmaster_id')->nullable()->constrained('webmasters')->nullOnDelete()->after('status');
        });

        Schema::table('webmasters', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['webmaster_id']);
            $table->dropColumn('webmaster_id');
        });

        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
