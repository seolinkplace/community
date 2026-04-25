<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->enum('platform_type', ['website','facebook','instagram','tiktok','linkedin','telegram','youtube','twitter'])->default('website')->after('domain');
            $table->string('platform_url')->nullable()->after('platform_type');
            $table->unsignedInteger('followers_count')->default(0)->after('platform_url');
            $table->boolean('first_post_published')->default(false)->after('followers_count');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['platform_type', 'platform_url', 'followers_count', 'first_post_published']);
        });
    }
};
