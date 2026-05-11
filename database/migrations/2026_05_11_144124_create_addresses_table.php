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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recipient_name');     // ФИО получателя
            $table->string('phone');              // Телефон для доставки
            $table->string('address');            // Полный адрес
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Россия');
            $table->text('comment')->nullable();   // Домофон, этаж, подъезд
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
