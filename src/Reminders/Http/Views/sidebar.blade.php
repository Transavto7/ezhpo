@php
    $accessToWorkdays = user()->access(
            'employees_workdays_read',
            'employees_workdays_create',
            'employees_workdays_report',
            'employees_workdays_holidays',
            'employees_workdays_tariffs'
        )
@endphp

@if($accessToWorkdays)
    <li>
        <a href="#" data-btn-collapse="#reminders" role="button"><i class="fa fa-sticky-note"></i>Напоминания</a>
        <ul id="reminders" class="collapse list-unstyle">
            @if(user()->access('employees_workdays_create'))
                <li>
                    <a href="{{ route('reminders.list-page') }}">Список напоминаний</a>
                </li>
            @endif
        </ul>
    </li>
@endif
