<?php
return [
    /*
     * Default settings visible fields in journals
     */
    'visible' => [
        'medic' => [
            'date' => true,
            'driver_fio' => true,
            'period_pl' => true,
            'created_at' => true,
            'driver_group_risk' => true,
            'type_view' => true,
            'realy' => true,
            'proba_alko' => true,
            'test_narko' => true,
        ],
        'tech' => [
            'date' => true,
            'car_gos_number' => true,
            'period_pl' => true,
            'created_at' => true,
            'car_type_auto' => true,
            'type_view' => true,
            'realy' => true,
        ],
        'bdd' => [
            'date' => true,
            'driver_fio' => true,
            'type_briefing' => true,
            'company_name' => true,
            'created_at' => true,
            'user_name' => true,
        ],
        'pechat_pl' => [
            'date' => true,
            'driver_fio' => true,
            'count_pl' => true,
            'company_name' => true,
            'user_name' => true,
            'pv_id' => true,
        ],
        'report_cart' => [
            'date' => true,
            'driver_fio' => true,
            'company_name' => true,
            'user_name' => true,
        ],
        'pak' => [
            'id' => true,
            'date' => true,
            'user_name' => true,
            'driver_gender' => true,
            'driver_year_birthday' => true,
            'complaint' => true,
            'condition_visible_sliz' => true,
            'condition_koj_pokr' => true,
            't_people' => true,
            'tonometer' => true,
            'pulse' => true,
            'proba_alko' => true,
            'admitted' => true,
            'user_eds' => true,
            'created_at' => true,
            'driver_group_risk' => true,
            'driver_fio' => true,
            'company_id' => true,
            'driver_id' => true,
            'photos' => true,
            'med_view' => true,
            'pv_id' => true,
            'car_mark_model' => true,
            'car_id' => true,
            'number_list_road' => true,
            'type_view' => true,
            'comments' => true,
            'flag_pak' => true,
        ]
    ],

    'client_exclude' => [
        'medic' => [
            'company_name',
            'company_id',
            'realy',
            'created_at',
            'flag_pak',
            'is_dop',
            't_people',
            'tonometer',
            'pulse',
            'period_pl',
            'date_prmo',
        ],
        'tech' => [
            'company_id',
            'company_name',
            'created_at',
            'realy',
            'is_dop',
            'date_prto',
            'period_pl'
        ],
        'bdd' => [
            'company_id',
            'company_name',
            'created_at',
        ]
    ]
];
