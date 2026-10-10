<?php

return [
    'active_driver' => $_ENV['HIMPRO_DRIVER'] ?? 'mock',

    'allowlist_tables' => [
        'pt.pt',
        'pt.ptallergy',
        'opd.opd',
        'opd.odiag',
        'opd.drug_order_opd',
        'ipd.ipd',
        'ipd.drug_order_ipd',
        'hosdata.user',
        'result_lab_opd'
    ],

    'default_limit' => 100,
    'max_limit' => 500,
    'query_timeout' => 5, // seconds

    'mappings' => [
        'patient' => [
            'hn' => 'hn',
            'first_name' => 'fname',
            'last_name' => 'lname',
            'birth_date' => 'birthday',
            'sex' => 'sex',
            'cid' => 'pop_id',
        ],
        'visit' => [
            'vn' => 'vn',
            'hn' => 'hn',
            'visit_date' => 'vdate',
            'department' => 'dep',
        ],
        'allergy' => [
            'hn' => 'hn',
            'drug_code' => 'dcode',
            'drug_name' => 'dname',
            'symptom' => 'symptom',
            'severity' => 'severity',
        ],
    ]
];
