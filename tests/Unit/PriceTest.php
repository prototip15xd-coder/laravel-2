<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function test_total_price_is_calculated(): void
    {
        $price = 1500;
        $quantity = 2;

        $total = $price * $quantity;

        $this->assertSame(3000, $total);
    }
}
