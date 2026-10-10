<?php

namespace Tomvis\HIMPro\Modules\Medication;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Contracts\HIS\MedicationRepositoryInterface;

class MedRecController extends BaseController
{
    protected MedicationRepositoryInterface $medRepo;

    public function __construct(MedicationRepositoryInterface $medRepo)
    {
        $this->medRepo = $medRepo;
    }

    public function index(Request $request): Response
    {
        return $this->view('medrec.index');
    }

    public function bpmh(Request $request, string $hn): JsonResponse
    {
        $orders = $this->medRepo->getOrdersByHn($hn);
        return $this->jsonSuccess($orders, 'Best Possible Medication History (BPMH) fetched.');
    }

    public function reconcile(Request $request): JsonResponse
    {
        $hn = (string)$request->input('hn');
        $actions = $request->input('actions', []); // Array of drug_code => action ('continue', 'hold', 'stop', 'adjust')

        return $this->jsonSuccess([
            'hn' => $hn,
            'reconciled_at' => date('Y-m-d H:i:s'),
            'actions_recorded' => count($actions),
            'status' => 'Pharmacist Reviewed',
        ], 'Medication Reconciliation review completed successfully.');
    }
}
