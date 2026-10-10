<?php

namespace Tomvis\HIMPro\Contracts\HIS;

interface AllergyRepositoryInterface
{
    public function getAllergiesByHn(string $hn): array;
}
