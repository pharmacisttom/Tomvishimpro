<?php

return [
    'session_lifetime' => 120, // 2 hours
    'max_login_attempts' => 5,
    'lockout_time' => 900, // 15 minutes
    'password_min_length' => 10,
    'require_special_char' => true,
    'roles' => [
        'super_admin' => 'Super Administrator',
        'hospital_admin' => 'Hospital Administrator',
        'it_admin' => 'IT Administrator',
        'security_officer' => 'Security Officer',
        'pharmacist' => 'Pharmacist',
        'physician' => 'Physician',
        'nurse' => 'Nurse',
        'medical_records_officer' => 'Medical Records Officer',
        'quality_officer' => 'Quality Officer',
        'auditor' => 'Auditor',
        'developer' => 'Developer',
        'sandbox_tester' => 'Sandbox Tester',
    ],
];
