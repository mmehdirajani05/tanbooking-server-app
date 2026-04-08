<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function overview(): JsonResponse
    {
        $overview = $this->dashboardService->getOverview();

        return $this->success('Dashboard overview retrieved.', $overview);
    }
}
