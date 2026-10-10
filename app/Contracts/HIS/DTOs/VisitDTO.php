<?php

namespace Tomvis\HIMPro\Contracts\HIS\DTOs;

class VisitDTO
{
    public function __construct(
        public string $vn,
        public string $hn,
        public string $visitDate,
        public string $department,
        public ?string $physician = null,
        public ?string $chiefComplaint = null
    ) {}

    public function toArray(): array
    {
        return [
            'vn' => $this->vn,
            'hn' => $this->hn,
            'visit_date' => $this->visitDate,
            'department' => $this->department,
            'physician' => $this->physician,
            'chief_complaint' => $this->chiefComplaint,
        ];
    }
}
