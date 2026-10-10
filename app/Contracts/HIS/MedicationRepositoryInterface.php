<?php

namespace Tomvis\HIMPro\Contracts\HIS;

interface MedicationRepositoryInterface
{
    public function getOrdersByHn(string $hn, int $limit = 50): array;
    public function getOrdersByVn(string $vn): array;
}
