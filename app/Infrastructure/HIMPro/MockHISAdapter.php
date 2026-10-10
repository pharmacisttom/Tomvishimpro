<?php

namespace Tomvis\HIMPro\Infrastructure\HIMPro;

use Tomvis\HIMPro\Contracts\HIS\PatientRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\VisitRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\DiagnosisRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\MedicationRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\AllergyRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\LaboratoryRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\AdmissionRepositoryInterface;
use Tomvis\HIMPro\Contracts\HIS\DTOs\PatientDTO;
use Tomvis\HIMPro\Contracts\HIS\DTOs\VisitDTO;
use Tomvis\HIMPro\Contracts\HIS\DTOs\AllergyDTO;
use Tomvis\HIMPro\Contracts\HIS\DTOs\MedicationDTO;
use Tomvis\HIMPro\Contracts\HIS\DTOs\LabResultDTO;
use Tomvis\HIMPro\Modules\Sandbox\SyntheticPatientGenerator;

class MockHISAdapter implements
    PatientRepositoryInterface,
    VisitRepositoryInterface,
    DiagnosisRepositoryInterface,
    MedicationRepositoryInterface,
    AllergyRepositoryInterface,
    LaboratoryRepositoryInterface,
    AdmissionRepositoryInterface
{
    protected SyntheticPatientGenerator $generator;

    public function __construct(?SyntheticPatientGenerator $generator = null)
    {
        $this->generator = $generator ?: new SyntheticPatientGenerator(42);
    }

    public function getByHn(string $hn): ?PatientDTO
    {
        $patient = $this->generator->getPatientByHn($hn);
        if (!$patient) {
            return null;
        }

        $allergies = array_map(function ($a) use ($hn) {
            return new AllergyDTO($hn, $a['drug_code'], $a['drug_name'], $a['symptom'], $a['severity'], $a['recorded_date']);
        }, $patient['allergies'] ?? []);

        $visits = array_map(function ($v) use ($hn) {
            return new VisitDTO($v['vn'], $hn, $v['visit_date'], $v['department'], $v['physician'], $v['chief_complaint']);
        }, $patient['visits'] ?? []);

        return new PatientDTO(
            $patient['hn'],
            $patient['full_name'],
            $patient['birth_date'],
            $patient['sex'],
            $patient['cid'],
            $patient['blood_group'],
            $patient['phone'],
            $allergies,
            $visits
        );
    }

    public function search(string $query, int $limit = 20): array
    {
        $patients = $this->generator->searchPatients($query, $limit);
        return array_map(function ($p) {
            return new PatientDTO(
                $p['hn'],
                $p['full_name'],
                $p['birth_date'],
                $p['sex'],
                $p['cid'],
                $p['blood_group'],
                $p['phone']
            );
        }, $patients);
    }

    public function getByVn(string $vn): ?VisitDTO
    {
        $v = $this->generator->getVisitByVn($vn);
        if (!$v) return null;
        return new VisitDTO($v['vn'], $v['hn'], $v['visit_date'], $v['department'], $v['physician'], $v['chief_complaint']);
    }

    public function getVisitsByHn(string $hn, int $limit = 20): array
    {
        $patient = $this->getByHn($hn);
        return $patient ? $patient->recentVisits : [];
    }

    public function getDiagnosisByVn(string $vn): array
    {
        return $this->generator->getDiagnosisByVn($vn);
    }

    public function getOrdersByHn(string $hn, int $limit = 50): array
    {
        $orders = $this->generator->getMedicationsByHn($hn);
        return array_map(function ($m) use ($hn) {
            return new MedicationDTO($hn, $m['vn'] ?? null, $m['drug_code'], $m['drug_name'], $m['dose'], $m['frequency'], $m['route'], $m['order_date']);
        }, $orders);
    }

    public function getOrdersByVn(string $vn): array
    {
        $orders = $this->generator->getMedicationsByVn($vn);
        return array_map(function ($m) {
            return new MedicationDTO($m['hn'], $vn, $m['drug_code'], $m['drug_name'], $m['dose'], $m['frequency'], $m['route'], $m['order_date']);
        }, $orders);
    }

    public function getAllergiesByHn(string $hn): array
    {
        $patient = $this->getByHn($hn);
        return $patient ? $patient->allergies : [];
    }

    public function getLabResultsByHn(string $hn, int $limit = 50): array
    {
        $labs = $this->generator->getLabsByHn($hn);
        return array_map(function ($l) use ($hn) {
            return new LabResultDTO($hn, $l['lab_code'], $l['lab_name'], $l['result_value'], $l['unit'], $l['normal_range'], $l['result_date']);
        }, $labs);
    }

    public function getAdmissionByAn(string $an): ?array
    {
        return $this->generator->getAdmissionByAn($an);
    }

    public function getAdmissionsByHn(string $hn): array
    {
        return $this->generator->getAdmissionsByHn($hn);
    }
}
