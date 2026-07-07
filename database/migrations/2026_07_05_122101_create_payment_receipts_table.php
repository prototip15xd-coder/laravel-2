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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();                              // id
            $table->foreignId('order_payment_id')->constrained('order_payments')->onDelete('cascade');
            $table->string('external_receipt_id')->nullable(); // внешний ключ на orders
            $table->string('type')->default('payment'); //  что значит тип???
            $table->string('status')->default('pending'); // статус платежа
            $table->boolean('send_to_customer')->default(false); // ссылка на оплату
            $table->json('request_payload')->nullable(); // запрос к YooKassa
            $table->json('response_payload')->nullable(); // ответ от YooKassa
            $table->text('error_message')->nullable();  // ошибка
            $table->timestamps();                       // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
