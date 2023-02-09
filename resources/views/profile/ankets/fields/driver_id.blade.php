@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Driver',
        'getField' => 'fio',
        'getFieldKey' => 'hash_id',
        'multiple' => 1,
        'concatField' => 'hash_id'
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
