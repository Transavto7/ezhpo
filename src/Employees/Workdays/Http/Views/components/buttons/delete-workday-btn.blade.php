@php
    /** @var \Src\Employees\Workdays\Eloquent\Workday $workday */
    $disabled = $workday->open_workday_id !== null;
    $trash = request()->get('trash', 0) ?? 1;
    $icon = $trash ? "fa-undo" : "fa-trash";
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
