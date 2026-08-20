<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Admin\SalesReportService;
use Illuminate\Console\Command;

class GenerateSalesReportCommand extends Command
{
    protected $signature = 'reports:sales';  // имя команды
    protected $description = 'Generate daily sales report';

    public function handle(SalesReportService $service): void
    {
        $service->refreshRecentReports();
        $this->info('Sales report generated successfully!');
    }
}
