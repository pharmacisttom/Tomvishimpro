<?php

namespace Tomvis\HIMPro\Contracts\HIS;

interface AdmissionRepositoryInterface
{
    public function getAdmissionByAn(string $an): ?array;
    public function getAdmissionsByHn(string $hn): array;
}
