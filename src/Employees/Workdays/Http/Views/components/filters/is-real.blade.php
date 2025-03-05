<div class="form-group">
    <label>Осмотр реальный</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => [
                0 => 'Нет',
                1 => 'Да'
            ],
        ],
        'model' => 'workdays',
        'k' => 'is_real',
        'is_required' => '',
        'default_value' => request()->get('is_real', null)
    ])
</div>
