<?php

namespace Tomvis\HIMPro\Contracts\HIS\FHIR;

use Tomvis\HIMPro\Contracts\HIS\DTOs\PatientDTO;

class FHIRPatient
{
    public static function fromPatientDTO(PatientDTO $dto): array
    {
        $nameParts = explode(' ', $dto->fullName, 2);
        $given = $nameParts[0] ?? '';
        $family = $nameParts[1] ?? '';

        return [
            'resourceType' => 'Patient',
            'id' => $dto->hn,
            'identifier' => [
                [
                    'system' => 'http://hospital.local/identifier/hn',
                    'value' => $dto->hn,
                ],
                [
                    'system' => 'http://hl7.org/fhir/sid/th-cid',
                    'value' => $dto->cid,
                ]
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => $family,
                    'given' => [$given],
                ]
            ],
            'gender' => strtolower($dto->sex) === 'm' ? 'male' : 'female',
            'birthDate' => $dto->birthDate,
            'telecom' => [
                [
                    'system' => 'phone',
                    'value' => $dto->phone,
                ]
            ]
        ];
    }
}
