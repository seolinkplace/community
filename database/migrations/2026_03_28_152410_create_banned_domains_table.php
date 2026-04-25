<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->enum('category', ['adult', 'spam', 'malware', 'competitor', 'phishing', 'other'])->default('other');
            $table->string('reason')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('banned_domains');
    }
};
