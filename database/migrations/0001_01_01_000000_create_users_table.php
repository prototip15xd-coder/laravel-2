<?php

namespace database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()
                ->comment('Первичный ключ пользователя');
            $table->string('first_name')
                ->comment('Имя пользователя');
            $table->string('last_name')
                ->comment('Фамилия пользователя');
            $table->string('email')
                ->unique()
                ->comment('Уникальный адрес электронной почты');
            $table->string('phone', 20)
                ->nullable()
                ->unique()
                ->comment('Номер телефона пользователя');
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ продукта');
            $table->string('name')->comment('Название продукта');
            $table->text('description')->nullable()->comment('Описание продукта');
            $table->decimal('price', 12, 2)
                ->comment('Цена продукта');
            $table->string('image')
                ->nullable()->comment('URL изображения продукта');
            $table->timestamps();
        });

        DB::statement("CREATE TYPE cart_status AS ENUM ('active', 'ordered', 'abandoned')");

        Schema::create('carts', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ корзины');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Пользователь, владелец корзины');
            $table->enum('status', ['active', 'ordered', 'abandoned'])
                ->default('active')
                ->comment('Статус корзины: active, ordered, abandoned');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ позиции корзины');
            $table->foreignId('cart_id')->constrained()->onDelete('cascade')->comment('Связь с корзиной');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('Связь с продуктом');
            $table->integer('quantity')->default(1)->comment('Количество товара');
            $table->decimal('price', 12, 2)->comment('Цена за единицу товара');
            $table->timestamps();
        });

        DB::statement("CREATE TYPE order_status AS ENUM ('pending', 'paid', 'shipped', 'completed', 'canceled')");

        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ заказа');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Покупатель');
            $table->decimal('total', 12, 2)->comment('Общая сумма заказа');
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'canceled'])
                ->default('pending')
                ->comment('Статус заказа');
            $table->text('shipping_address')->nullable()->comment('Адрес доставки');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ позиции заказа');
            $table->foreignId('order_id')->constrained()->onDelete('cascade')->comment('Связь с заказом');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('Связь с продуктом');
            $table->integer('quantity')->comment('Количество товара');
            $table->decimal('price', 12, 2)->comment('Цена за единицу товара на момент покупки');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');

        DB::statement('DROP TYPE IF EXISTS cart_status');
        DB::statement('DROP TYPE IF EXISTS order_status');
    }
};
