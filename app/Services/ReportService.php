<?php

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(private ReportRepository $reportRepository) {}

    public function getOutstandingReport(array $filters = [])
    {
        return $this->reportRepository->getOutstandingReport($filters);
    }

    public function getTopCustomers(array $filters = [])
    {
        return $this->reportRepository->getTopCustomers($filters);
    }
}
