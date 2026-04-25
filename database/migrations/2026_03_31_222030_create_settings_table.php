<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, bool, int
            $table->string('label')->nullable();
            $table->timestamps();
        });
        DB::table('settings')->insert([
            ['key' => 'registration_enabled', 'value' => '1', 'type' => 'bool', 'label' => 'Реєстрація нових користувачів', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
