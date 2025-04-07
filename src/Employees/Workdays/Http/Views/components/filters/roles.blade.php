<div class="form-group">
    <label>Роли</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => [
                'medic' => 'Медик',
                'tech' => 'Механик'
            ],
        ],
        'model' => 'workdays',
        'k' => 'roles',
        'is_required' => '',
        'default_value' => request()->get('roles', [])
    ])
</div>
