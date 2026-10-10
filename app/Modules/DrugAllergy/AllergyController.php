<?php

namespace Tomvis\HIMPro\Modules\DrugAllergy;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Contracts\HIS\AllergyRepositoryInterface;

class AllergyController extends BaseController
{
    protected AllergyRepositoryInterface $allergyRepo;

    public function __construct(AllergyRepositoryInterface $allergyRepo)
    {
        $this->allergyRepo = $allergyRepo;
    }

    public function index(Request $request): Response
    {
        return $this->view('allergy.index');
    }

    public function calculateNaranjo(Request $request): JsonResponse
    {
        // 10 Standard Naranjo ADR Probability Scale Questions
        $scores = [
            'q1' => (int)$request->input('q1', 0), // Previous conclusive reports (+1)
            'q2' => (int)$request->input('q2', 0), // Adverse event appeared after drug (+2)
            'q3' => (int)$request->input('q3', 0), // Improved when drug stopped (+1)
            'q4' => (int)$request->input('q4', 0), // Reappeared when readministered (+2)
            'q5' => (int)$request->input('q5', 0), // Alternative causes present (-1)
            'q6' => (int)$request->input('q6', 0), // Reaction appeared with placebo (+1)
            'q7' => (int)$request->input('q7', 0), // Drug detected in blood (+1)
            'q8' => (int)$request->input('q8', 0), // Reaction severe with higher dose (+1)
            'q9' => (int)$request->input('q9', 0), // Similar reaction to similar drugs (+1)
            'q10' => (int)$request->input('q10', 0), // Confirmed by objective evidence (+1)
        ];

        $totalScore = array_sum($scores);

        $category = 'Unlikely';
        if ($totalScore >= 9) {
            $category = 'Definite';
        } elseif ($totalScore >= 5) {
            $category = 'Probable';
        } elseif ($totalScore >= 1) {
            $category = 'Possible';
        }

        return $this->jsonSuccess([
            'total_score' => $totalScore,
            'category' => $category,
            'breakdown' => $scores,
        ], 'Naranjo ADR probability calculated successfully.');
    }
}
