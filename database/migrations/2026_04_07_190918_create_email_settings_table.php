<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->boolean('enabled')->default(true);
            $table->string('label');
            $table->timestamps();
        });

        // Seed default settings
        $types = [
            'all'                => 'Всі листи (майстер-вимикач)',
            'order_created'      => 'Нове замовлення (вебмастеру)',
            'order_approved'     => 'Замовлення апровнуто (клієнту)',
            'order_rejected'     => 'Замовлення відхилено (клієнту)',
            'article_submitted'  => 'Стаття надіслана (вебмастеру)',
            'article_approved'   => 'Стаття апровнута (клієнту)',
            'article_rejected'   => 'Стаття відхилена (клієнту)',
            'withdrawal_request' => 'Запит на виведення (адміну)',
        ];
        foreach ($types as $key => $label) {
            \DB::table('email_settings')->insert(['key' => $key, 'enabled' => true, 'label' => $label, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
