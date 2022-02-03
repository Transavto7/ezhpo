@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'Предрейсовый' => 'Предрейсовый',
            'Послерейсовый' => 'Послерейсовый',
            'Предсменный' => 'Предсменный',
            'Послесменный' => 'Послесменный'
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
