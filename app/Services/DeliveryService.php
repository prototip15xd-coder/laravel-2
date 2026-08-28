<?php

declare(strict_types=1);

namespace App\Services;

class DeliveryService
{
    public function delivery_total_cost($total): int
    {
        if ($total > 50000) {
            return 0;
        } elseif ($total > 10000) {
            return 500;
        } else {
            return 1000;
        }
    }

}
