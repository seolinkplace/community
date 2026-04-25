<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('site_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->unique(['site_id', 'language_code']);
        });

        // Мігруємо існуючі дані з поля language
        DB::table('sites')->whereNotNull('language')->get()->each(function($site) {
            $langs = array_map('trim', explode(',', $site->language));
            foreach ($langs as $lang) {
                if (!$lang) continue;
                DB::table('site_languages')->insertOrIgnore([
                    'site_id'       => $site->id,
                    'language_code' => mb_strtolower(substr($lang, 0, 10)),
                ]);
            }
        });
    }
    public function down(): void {
        Schema::dropIfExists('site_languages');
    }
};
