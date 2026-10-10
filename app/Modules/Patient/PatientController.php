<?php

namespace Tomvis\HIMPro\Modules\Patient;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Contracts\HIS\PatientRepositoryInterface;

class PatientController extends BaseController
{
    protected PatientRepositoryInterface $patientRepo;

    public function __construct(PatientRepositoryInterface $patientRepo)
    {
        $this->patientRepo = $patientRepo;
    }

    public function searchView(Request $request): Response
    {
        return $this->view('patient.search');
    }

    public function searchApi(Request $request): JsonResponse
    {
        $query = (string)$request->input('q', '');
        if (strlen($query) < 2) {
            return $this->jsonSuccess([], 'Search query too short.');
        }

        $patients = $this->patientRepo->search($query);
        $arrayData = array_map(fn($p) => $p->toArray(), $patients);

        return $this->jsonSuccess($arrayData, 'Patient search results retrieved.');
    }

    public function showApi(Request $request, string $hn): JsonResponse
    {
        $patient = $this->patientRepo->getByHn($hn);
        if (!$patient) {
            return $this->jsonError('Patient not found.', 404);
        }

        return $this->jsonSuccess($patient->toArray(), 'Patient records retrieved.');
    }
}
