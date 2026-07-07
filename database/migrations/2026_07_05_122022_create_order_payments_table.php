<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();                              // id
            $table->foreignId('order_id')->constrained(); // внешний ключ на orders
            $table->string('provider')->default('yookassa'); // провайдер
            $table->string('status')->default('pending'); // статус платежа
            $table->decimal('amount', 12, 2);          // сумма
            $table->string('currency')->default('RUB'); // валюта
            $table->string('external_payment_id')->nullable(); // ID в YooKassa
            $table->string('confirmation_url')->nullable(); // ссылка на оплату
            $table->json('request_payload')->nullable(); // запрос к YooKassa
            $table->json('response_payload')->nullable(); // ответ от YooKassa
            $table->text('error_message')->nullable();  // ошибка
            $table->timestamp('paid_at')->nullable();   // дата оплаты
            $table->timestamp('canceled_at')->nullable(); // дата отмены
            $table->timestamps();                       // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
