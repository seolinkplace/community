<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->date('domain_registered_at')->nullable()->after('traffic');
            $table->date('domain_expires_at')->nullable()->after('domain_registered_at');
            $table->unsignedTinyInteger('spam_score')->nullable()->after('domain_expires_at');
            $table->unsignedInteger('pages_count')->default(0)->after('spam_score');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['domain_registered_at', 'domain_expires_at', 'spam_score', 'pages_count']);
        });
    }
};
