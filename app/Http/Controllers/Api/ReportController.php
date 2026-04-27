<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ReportController extends Controller
{
    use ApiResponse;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function topProducts(): JsonResponse
    {
        return $this->successResponse($this->reportService->getTopProducts(), 'Top products report retrieved successfully.');
    }

    public function topCategories(): JsonResponse
    {
        return $this->successResponse($this->reportService->getTopCategories(), 'Top categories report retrieved successfully.');
    }

    public function revenue(): JsonResponse
    {
        return $this->successResponse($this->reportService->getTotalRevenue(), 'Total revenue report retrieved successfully.');
    }

    public function perSupplier(): JsonResponse
    {
        return $this->successResponse($this->reportService->getPerSupplierReport(), 'Per supplier report retrieved successfully.');
    }
}
