<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Drop FK and unique index that includes webmaster_id
            $table->dropForeign('sites_webmaster_id_foreign');
            $table->dropUnique('sites_webmaster_id_domain_unique');
        });

        Schema::table('sites', function (Blueprint $table) {
            // Make nullable, add unique without FK
            $table->unsignedBigInteger('webmaster_id')->nullable()->change();
            $table->unique(['webmaster_id', 'domain'], 'sites_webmaster_id_domain_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropUnique('sites_webmaster_id_domain_unique');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedBigInteger('webmaster_id')->nullable(false)->change();
            $table->unique(['webmaster_id', 'domain'], 'sites_webmaster_id_domain_unique');
            $table->foreign('webmaster_id', 'sites_webmaster_id_foreign')
                ->references('id')->on('webmasters')->onDelete('cascade');
        });
    }
};
