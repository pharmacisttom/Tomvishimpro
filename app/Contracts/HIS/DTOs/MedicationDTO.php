<?php

namespace Tomvis\HIMPro\Contracts\HIS\DTOs;

class MedicationDTO
{
    public function __construct(
        public string $hn,
        public ?string $vn,
        public string $drugCode,
        public string $drugName,
        public string $dose,
        public string $frequency,
        public string $route,
        public ?string $orderDate = null
    ) {}

    public function toArray(): array
    {
        return [
            'hn' => $this->hn,
            'vn' => $this->vn,
            'drug_code' => $this->drugCode,
            'drug_name' => $this->drugName,
            'dose' => $this->dose,
            'frequency' => $this->frequency,
            'route' => $this->route,
            'order_date' => $this->orderDate,
        ];
    }
}
