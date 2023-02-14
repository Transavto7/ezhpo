@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Car',
        'getField' => 'gos_number',
        'getFieldKey' => 'gos_number',
        'multiple' => 1,
        'trashed' => true
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
