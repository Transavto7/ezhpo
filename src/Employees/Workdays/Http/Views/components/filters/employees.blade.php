<div class="form-group">
    <label>Сотрудник</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => 'Employee',
            'getField' => 'name',
            'getFieldKey' => 'id',
            'multiple' => 1,
            'concatField' => 'hash_id',
            'trashed' => true
        ],
        'model' => 'workdays',
        'k' => 'employee_id',
        'is_required' => '',
        'default_value' => request()->get('employee_id', null) !== [null]
            ? request()->get('employee_id', null)
            : null
    ])
</div>
