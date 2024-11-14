@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            '1' => 'Да',
            '0' => 'Нет'
        ]
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value ?? []
])
