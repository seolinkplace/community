<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token']);
        });

        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token', 'verification_token']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->unique()->after('name');
            $table->string('password')->after('email');
            $table->rememberToken();
        });

        Schema::table('webmasters', function (Blueprint $table) {
            $table->string('email')->unique()->after('name');
            $table->string('password')->after('email');
            $table->string('verification_token')->nullable()->after('password');
            $table->rememberToken();
        });
    }
};
