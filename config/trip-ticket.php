<?php

return [
    'print' => [
        'template' => storage_path('app/templates/trip-tickets/print.xlsx'),
        'stamps' => [
            'medic' => [
                'reqName' => 'ООО "Трансавто-7"',
                'license' => 'Бессрочная лицензия от 09.12.2020 № Л041-1177-91/00366739',
                'comment' => <<<TEXT
                    ПРОШЕЛ ПРЕДРЕЙСОВЫЙ МЕДИЦИНСКИЙ
                    ОСМОТР. К ИСПОЛНЕНИЮ ТРУДОВЫХ
                    ОБЯЗАННОСТЕЙ ДОПУЩЕН
                    TEXT
            ],
            'tech' => 'ВЫПУСК НА ЛИНИЮ РАЗРЕШЕН',
        ],
        '4s' => [
            'template' => [
                'front' => [
                    'sheet' => '4s',
                    'prefix' => '4-С'
                ],
                'reverse' => [
                    'sheet' => '4s-reverse',
                    'prefix' => '4-С обр'
                ],
            ],
        ],
        '3' => [
            'template' => [
                'front' => [
                    'sheet' => '3',
                    'prefix' => '3'
                ],
                'reverse' => [
                    'sheet' => '3-reverse',
                    'prefix' => '3 обр'
                ],
            ],
        ],
        '4p' => [
            'template' => [
                'front' => [
                    'sheet' => '4p',
                    'prefix' => '4-П'
                ],
                'reverse' => [
                    'sheet' => '4p-reverse',
                    'prefix' => '4-П обр'
                ],
            ],
        ],
        'pg1' => [
            'template' => [
                'front' => [
                    'sheet' => 'pg1',
                    'prefix' => 'ПГ-1'
                ],
            ],
        ],
        '6c' => [
            'template' => [
                'front' => [
                    'sheet' => '6c',
                    'prefix' => '6-С'
                ],
                'reverse' => [
                    'sheet' => '6c-reverse',
                    'prefix' => '6-С обр'
                ],
            ],
        ],
        '3c' => [
            'template' => [
                'front' => [
                    'sheet' => '3c',
                    'prefix' => '3-С'
                ],
                'reverse' => [
                    'sheet' => '3c-reverse',
                    'prefix' => '3-С обр'
                ],
            ],
        ],
        '4o' => [
            'template' => [
                'front' => [
                    'sheet' => '4o',
                    'prefix' => '4-О'
                ],
                'reverse' => [
                    'sheet' => '4o-reverse',
                    'prefix' => '4-О обр'
                ],
            ],
        ],
        'ecm2' => [
            'template' => [
                'front' => [
                    'sheet' => 'ecm2',
                    'prefix' => 'ЭСМ-2'
                ],
                'reverse' => [
                    'sheet' => 'ecm2-reverse',
                    'prefix' => 'ЭСМ-2 обр'
                ],
            ],
        ],
    ],
];
