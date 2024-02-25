@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'Допущен' => 'Допущен',
            'Не допущен' => 'Не допущен',
            'Не идентифицирован' => 'Не идентифицирован'
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
