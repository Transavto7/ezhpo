@php
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
@endphp
<div class="form-group">
    <label>Тип осмотра</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => WorkdayEventTypeEnum::cases(),
        ],
        'model' => 'workdays',
        'k' => 'type_anketa',
        'is_required' => '',
        'default_value' => request()->get('type_anketa', null)
    ])
</div>
