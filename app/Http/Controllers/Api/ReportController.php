<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function topProducts(): JsonResponse
    {
        return $this->sendResponse($this->reportService->getTopProducts(), 'Top products report retrieved successfully.');
    }

    public function topCategories(): JsonResponse
    {
        return $this->sendResponse($this->reportService->getTopCategories(), 'Top categories report retrieved successfully.');
    }

    public function revenuePerWarehouse(): JsonResponse
    {
        return $this->sendResponse($this->reportService->getRevenuePerWarehouse(), 'Revenue per warehouse report retrieved successfully.');
    }

    public function perSupplier(): JsonResponse
    {
        return $this->sendResponse($this->reportService->getPerSupplierReport(), 'Per supplier report retrieved successfully.');
    }
}
