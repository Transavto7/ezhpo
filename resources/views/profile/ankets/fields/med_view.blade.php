@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            'В норме' => 'В норме',
            'Отстранения' => 'Отстранения'
        ],
        'multiple' => 1
    ],
    'model' => $type_ankets,
    'k' => $field,
    'is_required' => '',
    'default_value' => $field_default_value
])
