<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->timestamp('gdpr_consent_at')->nullable()->after('status');
            $table->string('gdpr_consent_ip', 45)->nullable()->after('gdpr_consent_at');
            $table->boolean('gdpr_deleted')->default(false)->after('gdpr_consent_ip');
            $table->timestamp('gdpr_deleted_at')->nullable()->after('gdpr_deleted');
        });
    }

    public function down(): void
    {
        Schema::table('unified_users', function (Blueprint $table) {
            $table->dropColumn(['gdpr_consent_at', 'gdpr_consent_ip', 'gdpr_deleted', 'gdpr_deleted_at']);
        });
    }
};
