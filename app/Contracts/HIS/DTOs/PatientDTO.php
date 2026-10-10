<?php

namespace Tomvis\HIMPro\Contracts\HIS\DTOs;

class PatientDTO
{
    public function __construct(
        public string $hn,
        public string $fullName,
        public string $birthDate,
        public string $sex,
        public ?string $cid = null,
        public ?string $bloodGroup = null,
        public ?string $phone = null,
        public array $allergies = [],
        public array $recentVisits = []
    ) {}

    public function toArray(): array
    {
        return [
            'hn' => $this->hn,
            'full_name' => $this->fullName,
            'birth_date' => $this->birthDate,
            'sex' => $this->sex,
            'cid' => $this->cid,
            'blood_group' => $this->bloodGroup,
            'phone' => $this->phone,
            'allergies' => $this->allergies,
            'recent_visits' => $this->recentVisits,
        ];
    }
}
