@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'true' => 'Да',
            'false' => 'Нет',
        ],
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])

