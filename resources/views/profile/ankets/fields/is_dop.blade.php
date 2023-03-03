@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            2 => 'Все',
            1 => 'Несогласованные',
            0 => 'Согласованные'
        ]
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
