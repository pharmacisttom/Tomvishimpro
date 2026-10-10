<?php

namespace Tomvis\HIMPro\Modules\Sandbox;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter;

class SandboxController extends BaseController
{
    protected SyntheticPatientGenerator $generator;
    protected MockHISAdapter $mockAdapter;

    public function __construct(SyntheticPatientGenerator $generator, MockHISAdapter $mockAdapter)
    {
        $this->generator = $generator;
        $this->mockAdapter = $mockAdapter;
    }

    public function index(Request $request): Response
    {
        $scenarios = [
            'no_allergy' => ['name' => 'Normal Patient (No Allergies)', 'hn' => '67000001', 'category' => 'General'],
            'penicillin_allergy' => ['name' => 'Penicillin Allergy Patient', 'hn' => '67000002', 'category' => 'Allergy'],
            'adr_history' => ['name' => 'Adverse Drug Reaction History', 'hn' => '67000003', 'category' => 'ADR'],
            'warfarin_therapy' => ['name' => 'Warfarin Anticoagulation', 'hn' => '67000004', 'category' => 'Clinical Pharmacy'],
            'ckd_stage4' => ['name' => 'Severe CKD (eGFR < 30)', 'hn' => '67000005', 'category' => 'Renal'],
        ];

        return $this->view('sandbox.dashboard', [
            'scenarios' => $scenarios,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $seed = (int)$request->input('seed', 42);
        $gen = new SyntheticPatientGenerator($seed);
        $sample = $gen->getPatientByHn('67000001');

        return $this->jsonSuccess([
            'seed' => $seed,
            'total_patients' => 50,
            'sample_patient' => $sample,
        ], 'Synthetic dataset generated successfully.');
    }

    public function inspectScenario(Request $request, string $code): JsonResponse
    {
        $hnMap = [
            'no_allergy' => '67000001',
            'penicillin_allergy' => '67000002',
            'adr_history' => '67000003',
            'warfarin_therapy' => '67000004',
            'ckd_stage4' => '67000005',
        ];

        $hn = $hnMap[$code] ?? '67000001';
        $patient = $this->mockAdapter->getByHn($hn);

        return $this->jsonSuccess([
            'scenario' => $code,
            'patient' => $patient ? $patient->toArray() : null,
            'medications' => $this->mockAdapter->getOrdersByHn($hn),
            'labs' => $this->mockAdapter->getLabResultsByHn($hn),
        ], "Scenario [{$code}] dataset inspected.");
    }
}
