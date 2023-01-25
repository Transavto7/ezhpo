@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Company',
        'getField' => 'hash_id',
        'getFieldKey' => 'hash_id',
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
