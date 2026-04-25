<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webmaster_client_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webmaster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('grace_hours')->default(0);
            $table->boolean('auto_restore')->default(true);
            $table->decimal('granted_balance', 10, 2)->default(0); // виділений баланс від вебмастера
            $table->timestamps();
            $table->unique(['webmaster_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webmaster_client_settings');
    }
};
