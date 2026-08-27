<?php

declare(strict_types=1);

namespace database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Создаём ENUM тип в PostgreSQL
        //DB::statement("CREATE TYPE cart_status AS ENUM ('active', 'ordered', 'abandoned')");
        $this->createEnumIfNotExists('cart_status', ['active', 'ordered', 'abandoned']);

        Schema::create('carts', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ корзины');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Пользователь, владелец корзины');
            $table->enum('status', ['active', 'ordered', 'abandoned'])
                ->default('active')
                ->comment('Статус корзины: active, ordered, abandoned');
            $table->timestamps();
        });
    }

    private function createEnumIfNotExists(string $enumName, array $values): void
    {
        // Проверяем существование ENUM
        /** @var \stdClass $result */
        $exists = DB::selectOne("
            SELECT EXISTS (
                SELECT 1
                FROM pg_type
                WHERE typname = ?
            ) as exists
        ", [$enumName]);

        if (!$exists->exists) {
            $valuesList = implode("', '", $values);
            DB::statement("CREATE TYPE {$enumName} AS ENUM ('{$valuesList}')");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
        DB::statement('DROP TYPE IF EXISTS cart_status');
    }
};
