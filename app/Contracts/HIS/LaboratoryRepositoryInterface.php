<?php

namespace Tomvis\HIMPro\Contracts\HIS;

interface LaboratoryRepositoryInterface
{
    public function getLabResultsByHn(string $hn, int $limit = 50): array;
}
