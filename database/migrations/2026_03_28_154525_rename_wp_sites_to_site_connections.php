<?php
use Illuminate\Database\Migrations\Migration;
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("RENAME TABLE wp_sites TO site_connections");
    }
    public function down(): void
    {
        DB::statement("RENAME TABLE site_connections TO wp_sites");
    }
};
