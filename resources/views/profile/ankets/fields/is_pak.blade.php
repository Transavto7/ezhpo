@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            '1' => 'Да',
            '0' => 'Нет'
        ]
    ],
    'model' => '',
    'k' => 'is_pak',
    'is_required' => '',
    'default_value' => $field_default_value
])
