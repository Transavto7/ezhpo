<div class="form-group">
    <label>ПВ</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => 'Point',
            'getField' => 'name',
            'getFieldKey' => 'id',
            'multiple' => 1,
            'concatField' => 'hash_id',
            'trashed' => true
        ],
        'model' => 'workdays',
        'k' => 'point_id',
        'is_required' => '',
        'default_value' => request()->get('point_id', [])
    ])
</div>
