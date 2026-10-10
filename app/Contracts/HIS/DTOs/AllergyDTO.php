<?php

namespace Tomvis\HIMPro\Contracts\HIS\DTOs;

class AllergyDTO
{
    public function __construct(
        public string $hn,
        public string $drugCode,
        public string $drugName,
        public string $symptom,
        public string $severity,
        public ?string $recordedDate = null
    ) {}

    public function toArray(): array
    {
        return [
            'hn' => $this->hn,
            'drug_code' => $this->drugCode,
            'drug_name' => $this->drugName,
            'symptom' => $this->symptom,
            'severity' => $this->severity,
            'recorded_date' => $this->recordedDate,
        ];
    }
}
