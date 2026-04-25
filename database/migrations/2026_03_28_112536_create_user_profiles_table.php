<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Профіль клієнта
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('unified_users')->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->string('plan')->default('free');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // Профіль вебмастера
        Schema::create('webmaster_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('unified_users')->cascadeOnDelete();
            $table->string('website')->nullable();
            $table->string('payment_details')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // Профіль виконавця (tasks)
        Schema::create('performer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('unified_users')->cascadeOnDelete();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('completions_count')->default(0);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performer_profiles');
        Schema::dropIfExists('webmaster_profiles');
        Schema::dropIfExists('client_profiles');
    }
};
