<?php

namespace Tomvis\HIMPro\Contracts\HIS\FHIR;

use Tomvis\HIMPro\Contracts\HIS\DTOs\AllergyDTO;

class FHIRAllergyIntolerance
{
    public static function fromAllergyDTO(AllergyDTO $dto): array
    {
        return [
            'resourceType' => 'AllergyIntolerance',
            'id' => md5($dto->hn . '_' . $dto->drugCode),
            'clinicalStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical',
                        'code' => 'active',
                    ]
                ]
            ],
            'verificationStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-verification',
                        'code' => 'confirmed',
                    ]
                ]
            ],
            'category' => ['medication'],
            'criticality' => strtolower($dto->severity) === 'severe' ? 'high' : 'low',
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://hospital.local/drug-code',
                        'code' => $dto->drugCode,
                        'display' => $dto->drugName,
                    ]
                ],
                'text' => $dto->drugName,
            ],
            'patient' => [
                'reference' => 'Patient/' . $dto->hn,
            ],
            'reaction' => [
                [
                    'manifestation' => [
                        [
                            'text' => $dto->symptom,
                        ]
                    ],
                    'severity' => strtolower($dto->severity) === 'severe' ? 'severe' : 'mild',
                ]
            ]
        ];
    }
}
