<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('_migration_map', function (Blueprint $table) {
            $table->id();
            $table->string('old_type');   // client / webmaster
            $table->unsignedBigInteger('old_id');
            $table->unsignedBigInteger('new_user_id');
            $table->timestamps();

            $table->unique(['old_type', 'old_id']);
            $table->index('new_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('_migration_map');
    }
};
