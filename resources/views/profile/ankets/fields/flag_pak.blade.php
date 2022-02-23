@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'СДПО А' => 'СДПО А',
            'СДПО Р' => 'СДПО Р'
        ]
    ],
    'model' => '',
    'k' => 'flag_pak',
    'is_required' => '',
    'default_value' => $field_default_value
])
