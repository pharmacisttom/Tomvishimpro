<?php

namespace Tomvis\HIMPro\Contracts\HIS;

use Tomvis\HIMPro\Contracts\HIS\DTOs\VisitDTO;

interface VisitRepositoryInterface
{
    public function getByVn(string $vn): ?VisitDTO;
    public function getVisitsByHn(string $hn, int $limit = 20): array;
}
