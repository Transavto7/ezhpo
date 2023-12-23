<?php

return [
    'guid' => '50e4fce5-ffeb-270c-07a1-4a7fd01dbee7',
    'idLPU' => 'ddcd8361-bedf-4ab6-8550-ac00622a01ac',

    // API
    'api_emk' => 'http://b2b-demo.n3health.ru/emk/EMKService.svc?wsdl',
    'api_pix' => 'http://b2b-demo.n3health.ru/emk/PixService.svc?wsdl',
    'api_term' => 'http://b2b-demo.n3health.ru/nsi/fhir/term/',

    // значения из терминологии
    'id_payment_type' => 3,
    'confidentiality' => 1,
    'doctor_confidentiality' => 1,
    'curator_confidentiality' => 1,
    'id_case_aid_type' => 6,
    'id_case_result' => 10,
    'case_visit_type' => 1,
    'id_case_type' => 2,
    'id_visit_place' => 1,
    'id_diagnosis_type' => 1,
    'mkb_code' => 'I10',
    'id_visit_purpose' => 5,

    'sex' => [
        [
            'code' => 1,
            'display' => 'мужской'
        ],
        [
            'code' => 2,
            'display' => 'женский'
        ],
        [
            'code' => 3,
            'display' => 'неопределенный'
        ],
    ],

    // формат даты
    'date_format' => 'Y-m-d',
    'date_time_format' => 'Y-m-d\TH:i:sP',
    'date_birth_format' => 'Y-m-d',

    // лицо, по которому передаются данные
    'person' => [
        'family_name' => 'Осинкина',
        'given_name' => 'Екатерина',
        'middle_name' => 'Анатольевна',
        'sex' => 2,
        'id_person_mis' => '76fe72d0-8766-46bb-a9f8-8b694175abe2',
        'id_position' => 159,
        'id_speciality' => 204,
    ]
];
