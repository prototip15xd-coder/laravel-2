<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\DeliveryService;
use PHPUnit\Framework\TestCase;

class DeliveryServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_delivery_cost_0(): void
    {
        $calculate = new DeliveryService();
        $this->assertSame(0, $calculate->delivery_total_cost(65000));
    }

    public function test_delivery_cost_500(): void
    {
        $calculate = new DeliveryService();
        $this->assertSame(500, $calculate->delivery_total_cost(45000));
    }

    public function test_delivery_cost_1000(): void
    {
        $calculate = new DeliveryService();
        $this->assertSame(1000, $calculate->delivery_total_cost(9000));
    }
}
