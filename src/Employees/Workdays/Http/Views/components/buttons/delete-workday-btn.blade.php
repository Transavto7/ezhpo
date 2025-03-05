@php
    /**
    * @var \Src\Employees\Workdays\Eloquent\Workday $workday
    * closed_workday_id - получается в запросе в контроллере, не аттрибут
    */
    $trash = request()->get('trash', 0) ?? 1;
    $icon = $trash ? "fa-undo" : "fa-trash";
    $disabled = ($workday->closed_workday_id !== null) && ($workday->type_anketa === \Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum::OPEN);
    $color = $disabled ? "btn-secondary" : "btn-warning";
    $route = $disabled ? '#' : route('employees.workdays.trash', ['id' => $workday->id, 'action' => $trash ? 0 : 1]);
@endphp

<a
    href="{{ $route }}"
    class="btn {{ $color }} btn-sm hv-btn-trash mr-1"
    data-id="{{ $workday->id }}"
    @disabled($disabled)>
    <i class="fa {{ $icon }}"></i>
</a>
