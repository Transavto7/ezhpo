@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Town',
        'getField' => 'name',
        'getFieldKey' => 'id',
        'multiple' => 1,
        'concatField' => 'hash_id',
        'trashed' => true,
        'orderBy' => 'name'
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
