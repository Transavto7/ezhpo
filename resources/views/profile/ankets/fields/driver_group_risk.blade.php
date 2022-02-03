@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'А/Д' => 'А/Д',
            'Возраст' => 'Возраст',
            'Алкоголь' => 'Алкоголь',
            'Наркотики' => 'Наркотики'
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
