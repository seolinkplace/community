<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // MariaDB: change enum to varchar directly
        DB::statement("ALTER TABLE sites MODIFY COLUMN platform_type VARCHAR(50) NOT NULL DEFAULT 'website'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sites MODIFY COLUMN platform_type ENUM('website','facebook','instagram','tiktok','linkedin','telegram','youtube','twitter') NOT NULL DEFAULT 'website'");
    }
};
