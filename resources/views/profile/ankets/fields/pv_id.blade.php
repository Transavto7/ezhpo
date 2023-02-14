@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Point',
        'getField' => 'name',
        'getFieldKey' => 'name',
        'multiple' => 1,
        'concatField' => 'hash_id',
        'trashed' => true,
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
