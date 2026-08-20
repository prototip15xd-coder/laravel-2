<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Admin\SalesReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly SalesReportService $salesReportService,
    ) {
    }
    public function index(): View
    {
        return view('admin.dashboard', [
            'report' => $this->salesReportService->getDashboardReport(),
        ]);
    }


}
