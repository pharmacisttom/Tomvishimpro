<?php

namespace Tomvis\HIMPro\Contracts\HIS;

interface DiagnosisRepositoryInterface
{
    public function getDiagnosisByVn(string $vn): array;
}
