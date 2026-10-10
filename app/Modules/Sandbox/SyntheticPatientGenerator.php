<?php

namespace Tomvis\HIMPro\Modules\Sandbox;

class SyntheticPatientGenerator
{
    protected int $seed;
    protected array $patients = [];

    protected array $thaiFirstNamesMale = ['สมชาย', 'วิชัย', 'ประเสริฐ', 'กิตติ', 'อนันต์', 'ธีรพงษ์', 'ศุภชัย', 'นพดล', 'สิทธิชัย', 'พิเชษฐ์'];
    protected array $thaiFirstNamesFemale = ['สมศรี', 'นภา', 'วันดี', 'ปราณี', 'สุมาลี', 'กนิษฐา', 'พัชรี', 'จิราภรณ์', 'ศิริพร', 'วรรณา'];
    protected array $thaiLastNames = ['ใจดี', 'มีสุข', 'วงษ์สุวรรณ', 'เจริญสุข', 'บุญมี', 'รุ่งเรือง', 'สุขเจริญ', 'ศรีสุข', 'ทองแก้ว', 'เพชรรัตน์'];
    protected array $departments = ['OPD-InternalMed', 'OPD-Pediatrics', 'OPD-Surgery', 'OPD-Ortho', 'IPD-MaleMed', 'IPD-FemaleMed', 'ER', 'ICU'];

    protected array $drugs = [
        ['code' => 'D001', 'name' => 'Amoxicillin 500 mg cap', 'dose' => '1 cap', 'freq' => 'tid pc', 'route' => 'PO'],
        ['code' => 'D002', 'name' => 'Paracetamol 500 mg tab', 'dose' => '1-2 tab', 'freq' => 'prn q 4-6 hr', 'route' => 'PO'],
        ['code' => 'D003', 'name' => 'Omeprazole 20 mg cap', 'dose' => '1 cap', 'freq' => 'bid ac', 'route' => 'PO'],
        ['code' => 'D004', 'name' => 'Warfarin 3 mg tab', 'dose' => '1 tab', 'freq' => 'od hs', 'route' => 'PO'],
        ['code' => 'D005', 'name' => 'Metformin 500 mg tab', 'dose' => '1 tab', 'freq' => 'bid pc', 'route' => 'PO'],
        ['code' => 'D006', 'name' => 'Enalapril 5 mg tab', 'dose' => '1 tab', 'freq' => 'od pc', 'route' => 'PO'],
        ['code' => 'D007', 'name' => 'Ceftriaxone 1 g vial', 'dose' => '1 g', 'freq' => 'od iv', 'route' => 'IV'],
        ['code' => 'D008', 'name' => 'Co-trimoxazole 800/160 tab', 'dose' => '1 tab', 'freq' => 'bid pc', 'route' => 'PO'],
    ];

    public function __construct(int $seed = 42)
    {
        $this->seed = $seed;
        $this->generateDataset();
    }

    protected function generateDataset(): void
    {
        mt_srand($this->seed);

        for ($i = 1; $i <= 50; $i++) {
            $hn = sprintf('67%06d', $i);
            $isMale = (mt_rand(0, 1) === 1);
            $sex = $isMale ? 'M' : 'F';
            $firstName = $isMale
                ? $this->thaiFirstNamesMale[array_rand($this->thaiFirstNamesMale)]
                : $this->thaiFirstNamesFemale[array_rand($this->thaiFirstNamesFemale)];
            $lastName = $this->thaiLastNames[array_rand($this->thaiLastNames)];
            $fullName = "{$firstName} {$lastName}";

            $year = mt_rand(1950, 2015);
            $month = sprintf('%02d', mt_rand(1, 12));
            $day = sprintf('%02d', mt_rand(1, 28));
            $birthDate = "{$year}-{$month}-{$day}";

            $cid = sprintf('1%02d999%05d', mt_rand(10, 99), $i);
            $bloodGroup = ['A', 'B', 'O', 'AB'][mt_rand(0, 3)];
            $phone = sprintf('08%08d', mt_rand(10000000, 99999999));

            // Generate Allergies
            $allergies = [];
            if ($i === 2 || $i === 12) {
                $allergies[] = [
                    'drug_code' => 'D001',
                    'drug_name' => 'Amoxicillin / Penicillin G',
                    'symptom' => 'Anaphylaxis, Urticaria',
                    'severity' => 'Severe',
                    'recorded_date' => '2025-03-10',
                ];
            } elseif ($i === 3 || $i === 15) {
                $allergies[] = [
                    'drug_code' => 'D008',
                    'drug_name' => 'Co-trimoxazole',
                    'symptom' => 'Stevens-Johnson Syndrome (SJS)',
                    'severity' => 'Critical',
                    'recorded_date' => '2024-11-20',
                ];
            }

            // Generate Visits
            $visits = [];
            $numVisits = mt_rand(1, 4);
            for ($v = 1; $v <= $numVisits; $v++) {
                $vn = sprintf('67%02d%04d', $v, $i);
                $dept = $this->departments[mt_rand(0, count($this->departments) - 1)];
                $visits[] = [
                    'vn' => $vn,
                    'hn' => $hn,
                    'visit_date' => date('Y-m-d', strtotime("-{$v} months")),
                    'department' => $dept,
                    'physician' => 'นพ. สมชาย รักษาดี',
                    'chief_complaint' => 'ไข้ ไอ เจ็บคอ 2 วันก่อนมาโรงพยาบาล',
                ];
            }

            // Generate Medications
            $medications = [];
            $numMeds = mt_rand(1, 3);
            for ($m = 0; $m < $numMeds; $m++) {
                $drug = $this->drugs[mt_rand(0, count($this->drugs) - 1)];
                $medications[] = [
                    'hn' => $hn,
                    'vn' => $visits[0]['vn'],
                    'drug_code' => $drug['code'],
                    'drug_name' => $drug['name'],
                    'dose' => $drug['dose'],
                    'frequency' => $drug['freq'],
                    'route' => $drug['route'],
                    'order_date' => $visits[0]['visit_date'],
                ];
            }

            // Generate Lab Results
            $labs = [
                [
                    'lab_code' => 'L001',
                    'lab_name' => 'Serum Creatinine',
                    'result_value' => ($i === 5 ? '3.8' : '0.9'),
                    'unit' => 'mg/dL',
                    'normal_range' => '0.6 - 1.2',
                    'result_date' => $visits[0]['visit_date'],
                ],
                [
                    'lab_code' => 'L002',
                    'lab_name' => 'eGFR',
                    'result_value' => ($i === 5 ? '18.5' : '95.2'),
                    'unit' => 'mL/min/1.73m2',
                    'normal_range' => '> 60',
                    'result_date' => $visits[0]['visit_date'],
                ],
                [
                    'lab_code' => 'L003',
                    'lab_name' => 'INR',
                    'result_value' => ($i === 4 ? '3.5' : '1.1'),
                    'unit' => '',
                    'normal_range' => '0.8 - 1.2',
                    'result_date' => $visits[0]['visit_date'],
                ],
            ];

            $this->patients[$hn] = [
                'hn' => $hn,
                'full_name' => $fullName,
                'birth_date' => $birthDate,
                'sex' => $sex,
                'cid' => $cid,
                'blood_group' => $bloodGroup,
                'phone' => $phone,
                'allergies' => $allergies,
                'visits' => $visits,
                'medications' => $medications,
                'labs' => $labs,
            ];
        }
    }

    public function getPatientByHn(string $hn): ?array
    {
        return $this->patients[$hn] ?? null;
    }

    public function searchPatients(string $query, int $limit = 20): array
    {
        $results = [];
        foreach ($this->patients as $p) {
            if (str_contains($p['hn'], $query) || str_contains($p['full_name'], $query) || str_contains($p['cid'], $query)) {
                $results[] = $p;
                if (count($results) >= $limit) break;
            }
        }
        return $results;
    }

    public function getVisitByVn(string $vn): ?array
    {
        foreach ($this->patients as $p) {
            foreach ($p['visits'] as $v) {
                if ($v['vn'] === $vn) return $v;
            }
        }
        return null;
    }

    public function getDiagnosisByVn(string $vn): array
    {
        return [
            ['icd10' => 'J00', 'icd_name' => 'Acute nasopharyngitis (common cold)', 'diag_type' => 'Primary Dx'],
            ['icd10' => 'I10', 'icd_name' => 'Essential (primary) hypertension', 'diag_type' => 'Comorbidity'],
        ];
    }

    public function getMedicationsByHn(string $hn): array
    {
        return $this->patients[$hn]['medications'] ?? [];
    }

    public function getMedicationsByVn(string $vn): array
    {
        $v = $this->getVisitByVn($vn);
        if (!$v) return [];
        return $this->getMedicationsByHn($v['hn']);
    }

    public function getLabsByHn(string $hn): array
    {
        return $this->patients[$hn]['labs'] ?? [];
    }

    public function getAdmissionByAn(string $an): ?array
    {
        return [
            'an' => $an,
            'hn' => '67000001',
            'admit_date' => date('Y-m-d', strtotime('-5 days')),
            'discharge_date' => null,
            'ward' => 'IPD-MaleMed',
            'bed' => '104-B',
            'attending_doctor' => 'นพ. สมชาย รักษาดี',
        ];
    }

    public function getAdmissionsByHn(string $hn): array
    {
        $adm = $this->getAdmissionByAn('AN67000101');
        $adm['hn'] = $hn;
        return [$adm];
    }
}
