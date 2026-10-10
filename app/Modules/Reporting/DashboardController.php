<?php

namespace Tomvis\HIMPro\Modules\Reporting;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter;

class DashboardController extends BaseController
{
    protected MockHISAdapter $mockAdapter;

    public function __construct(MockHISAdapter $mockAdapter)
    {
        $this->mockAdapter = $mockAdapter;
    }

    public function index(Request $request): Response
    {
        return $this->view('dashboard.index', [
            'total_patients' => 50,
            'total_visits' => 128,
            'total_allergies' => 4,
            'total_adrs' => 2,
            'security_score' => 98,
        ]);
    }

    public function metricsApi(Request $request): JsonResponse
    {
        return $this->jsonSuccess([
            'total_patients' => 50,
            'total_visits' => 128,
            'drug_allergy_count' => 4,
            'adr_count' => 2,
            'med_rec_completed' => 42,
            'monthly_visits' => [
                'Oct' => 12, 'Nov' => 18, 'Dec' => 25, 'Jan' => 30, 'Feb' => 22, 'Mar' => 21
            ],
            'top_allergies' => [
                'Penicillin' => 2,
                'Co-trimoxazole' => 1,
                'Aspirin' => 1
            ],
        ], 'Dashboard metrics retrieved successfully.');
    }
}
