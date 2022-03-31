@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'Допущен' => 'Допущен',
            'Недопущен' => 'Недопущен'
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
