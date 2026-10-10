<?php

namespace Tomvis\HIMPro\Contracts\HIS\DTOs;

class LabResultDTO
{
    public function __construct(
        public string $hn,
        public string $labCode,
        public string $labName,
        public string $resultValue,
        public string $unit,
        public ?string $normalRange = null,
        public ?string $resultDate = null
    ) {}

    public function toArray(): array
    {
        return [
            'hn' => $this->hn,
            'lab_code' => $this->labCode,
            'lab_name' => $this->labName,
            'result_value' => $this->resultValue,
            'unit' => $this->unit,
            'normal_range' => $this->normalRange,
            'result_date' => $this->resultDate,
        ];
    }
}
