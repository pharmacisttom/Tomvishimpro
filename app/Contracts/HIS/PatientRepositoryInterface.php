<?php

namespace Tomvis\HIMPro\Contracts\HIS;

use Tomvis\HIMPro\Contracts\HIS\DTOs\PatientDTO;

interface PatientRepositoryInterface
{
    public function getByHn(string $hn): ?PatientDTO;
    public function search(string $query, int $limit = 20): array;
}
