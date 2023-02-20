@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => 'Car',
        'getField' => 'mark_model',
        'getFieldKey' => 'mark_model',
        'multiple' => 1,
        'trashed' => true
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
