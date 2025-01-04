<?php

return [
    'print' => [
        'template' => public_path('templates/trip-tickets/print.xlsx'),
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
            'stamps' => [
                'medic' => <<<TEXT
                    Бессрочная лицензия от 28.09.2022
                    № Л041-01184-63/00618873
                    ПРОШЕЛ ПРЕДРЕЙСОВЫЙ МЕДИЦИНСКИЙ
                    ОСМОТР. К ИСПОЛНЕНИЮ ТРУДОВЫХ
                    ОБЯЗАННОСТЕЙ ДОПУЩЕН
                    TEXT,
                'tech' => 'ВЫПУСК НА ЛИНИЮ РАЗРЕШЕН',
            ],
        ],
    ],
];
