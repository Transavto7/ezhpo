@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'internal' => 'Очный',
            'СДПО А' => 'СДПО А',
            'СДПО Р' => 'СДПО Р'
        ],
        'multiple' => 1
    ],
    'model' => '',
    'k' => 'flag_pak',
    'is_required' => '',
    'default_value' => $field_default_value
])
