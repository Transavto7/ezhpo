@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'Предрейсовый/Предсменный' => 'Предрейсовый/Предсменный',
            'Послерейсовый/Послесменный' => 'Послерейсовый/Послесменный',
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
