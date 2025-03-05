<div class="form-group">
    <label>Смена закрыта</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => [
                0 => 'Нет',
                1 => 'Да'
            ],
        ],
        'model' => 'workdays',
        'k' => 'is_closed',
        'is_required' => '',
        'default_value' => request()->get('is_closed', null)
    ])
</div>
