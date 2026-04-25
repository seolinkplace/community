<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->dropUnique('page_prices_scope_unique');
            $table->unique(
                ['site_id', 'price_type', 'scope_type', 'scope_depth', 'scope_url', 'client_id'],
                'page_prices_scope_unique'
            );
        });
    }
    public function down(): void
    {
        Schema::table('page_prices', function (Blueprint $table) {
            $table->dropUnique('page_prices_scope_unique');
            $table->unique(
                ['site_id', 'scope_type', 'scope_depth', 'scope_url', 'client_id'],
                'page_prices_scope_unique'
            );
        });
    }
};
