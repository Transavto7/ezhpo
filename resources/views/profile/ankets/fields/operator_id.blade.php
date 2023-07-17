@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'User',
        'getField' => 'name',
        'getFieldKey' => 'id',
        'multiple' => 1,
        'concatField' => 'id',
        'trashed' => true
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
