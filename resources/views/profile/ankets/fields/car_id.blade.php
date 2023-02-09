@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Car',
        'getField' => 'gos_number',
        'getFieldKey' => 'hash_id',
        'multiple' => 1,
        'concat' => 'hash_id'
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
