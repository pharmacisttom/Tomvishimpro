<?php

return [
    'enabled' => filter_var($_ENV['SANDBOX_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'seed' => (int)($_ENV['SANDBOX_SEED'] ?? 42),
    'patient_count' => 50,
    'scenarios' => [
        'no_allergy' => 'Patient with no known drug allergies',
        'penicillin_allergy' => 'Patient with severe Penicillin allergy',
        'adr_history' => 'Patient with Adverse Drug Reaction (ADR) history',
        'warfarin_therapy' => 'Patient on Warfarin anticoagulant therapy',
        'ckd_stage4' => 'Patient with Chronic Kidney Disease (eGFR < 30)',
        'med_rec_admission' => 'IPD Admission requiring Medication Reconciliation',
        'high_alert_med' => 'Patient prescribed High-Alert Medication (Insulin/Heparin)',
        'multi_visit' => 'Patient with multiple OPD visits across departments',
    ],
];
