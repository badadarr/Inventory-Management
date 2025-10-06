<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function outstanding(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);

        return Inertia::render('Report/Outstanding', [
            'orders' => $this->reportService->getOutstandingReport($filters)
        ]);
    }

    public function topCustomers(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'limit']);

        return Inertia::render('Report/TopCustomers', [
            'customers' => $this->reportService->getTopCustomers($filters)
        ]);
    }
}
