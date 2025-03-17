<div class="form-group">
    <label>Допуск</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => [
                0 => 'Нет',
                1 => 'Да'
            ],
        ],
        'model' => 'workdays',
        'k' => 'admitted',
        'is_required' => '',
        'default_value' => request()->get('admitted', null)
    ])
</div>
