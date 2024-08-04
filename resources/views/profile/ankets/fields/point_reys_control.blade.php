@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'Пройден' => 'Пройден',
            'Не пройден' => 'Не пройден'
        ]
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
