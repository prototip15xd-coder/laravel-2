<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\DailySalesReport;
use App\Models\Order;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class SalesReportService
{
    private const REPORT_DAYS = 7;

    private const SUCCESSFUL_ORDER_STATUSES = [
        Order::STATUS_PAID,
        Order::STATUS_SHIPPED,
        Order::STATUS_COMPLETED,
    ];

    public function getBaseReport($days): array
    {
        $startDate = now()->subDays($days);

        $ordersCount = Order::where('created_at', '>=', $startDate)
            ->count();

        $salesCount = Order::query()
            ->whereIn('status', [
                Order::STATUS_PAID,
                Order::STATUS_SHIPPED,
                Order::STATUS_COMPLETED,
            ])->where('created_at', '>=', $startDate)
            ->count();

        $revenue = Order::where('status', Order::STATUS_PAID)
            ->where('created_at', '>=', $startDate)
            ->sum('total');

        $canceledCount = Order::where('status', Order::STATUS_CANCELED)
            ->where('created_at', '>=', $startDate)
            ->count();

        $dailySales = Order::where('status', Order::STATUS_PAID)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        $averageOrderValue = $salesCount > 0
            ? $revenue / $salesCount
            : 0;

        $pending_orders_count = Order::whereIn(
            'status',
            Order::STATUS_PENDING
        )
            ->count();

        return ['ordersCount' => $ordersCount,
            'salesCount' => $salesCount,
            'revenue' => $revenue,
            'canceledCount' => $canceledCount,
            'dailySales' => $dailySales,
            'averageOrderValue' => $averageOrderValue,
        ];
    }

    public function getReport($days): array
    {
        $report = $this->getBaseReport($days);
        $dailySales = $report['dailySales'];
        $period = CarbonPeriod::create(now()->subDays($days), now());
        $result = [];

        foreach ($period as $date) {
            $key = $date->toDateString();
            $result[$key] = isset($dailySales[$key]) ? (float) $dailySales[$key]->total : 0;
        }

        $report['dailySales'] = $result;
        return $report;
    }

    public function refreshRecentReports(): void
    {
        for ($daysAgo = 0; $daysAgo < self::REPORT_DAYS; $daysAgo++) {
            $date = now()
                ->startOfDay()
                ->subDays($daysAgo);

            $this->refreshReportForDate($date);
        }
    }

    public function refreshReportForDate(
        CarbonInterface $date
    ): DailySalesReport {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [
                $startOfDay,
                $endOfDay,
            ]);

        $ordersCount = (clone $ordersQuery)->count();

        $salesCount = (clone $ordersQuery)
            ->whereIn(
                'status',
                self::SUCCESSFUL_ORDER_STATUSES
            )
            ->count();

        $revenue = (clone $ordersQuery)
            ->whereIn(
                'status',
                self::SUCCESSFUL_ORDER_STATUSES
            )
            ->sum('total');

        $canceledCount = (clone $ordersQuery)
            ->where('status', Order::STATUS_CANCELED)
            ->count();

        $averageOrderValue = $salesCount > 0
            ? $revenue / $salesCount
            : 0;

        $pending_orders_count = (clone $ordersQuery)
            ->whereIn(
                'status',
                Order::STATUS_PENDING
            )
            ->count();

        return DailySalesReport::query()->updateOrCreate(
            [
                'report_date' => $date->toDateString(),
            ],
            [
                'orders_count' => $ordersCount,
                'sales_count' => $salesCount,
                'revenue' => $revenue,
                'canceled_count' => $canceledCount,
                'calculated_at' => now(),
                'average_order_value' => $averageOrderValue,
                'pending_orders_count' => $pending_orders_count,
            ]
        );
    }

    public function getRecentReports(): Collection
    {
        return DailySalesReport::query()
            ->whereDate(
                'report_date',
                '>=',
                now()->subDays(self::REPORT_DAYS - 1)->toDateString()
            )
            ->orderBy('report_date')
            ->get();
    }

    public function getDashboardReport(): array
    {
        $reports = $this->getRecentReports();

        return [
            'orders_count' => $reports->sum('orders_count'),
            'sales_count' => $reports->sum('sales_count'),
            'revenue' => $reports->sum(
                fn (DailySalesReport $report): float =>
                (float) $report->revenue
            ),
            'canceled_count' => $reports->sum('canceled_count'),
            'daily_reports' => $reports,
            'calculated_at' => $reports->max('calculated_at'),
            'pending_orders_count' => $reports->max('pending_orders_count'),
        ];
    }
}
