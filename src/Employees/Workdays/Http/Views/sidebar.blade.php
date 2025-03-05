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
        <a href="#" data-btn-collapse="#workdays" role="button"><i class="fa fa-clock-o"></i> Рабочее время</a>
        <ul id="workdays" class="collapse list-unstyle">
            @if(user()->access('employees_workdays_create'))
                <li>
                    <a href="{{ route('employees.workdays.create-page') }}">Добавить смену</a>
                </li>
            @endif
            @if(user()->access('employees_workdays_read'))
                <li>
                    <a href="{{ route('employees.workdays.index') }}">Журнал смен</a>
                </li>
            @endif
            @if(user()->access('employees_workdays_report'))
                <li>
                    <a href="{{ route('employees.workdays.report') }}">Расчет ЗП</a>
                </li>
            @endif
            @if(user()->access('employees_workdays_holidays'))
                <li>
                    <a href="{{ route('employees.holidays.index') }}">Нерабочие дни</a>
                </li>
            @endif
            @if(user()->access('employees_workdays_tariffs'))
                <li>
                    <a href="{{ route('employees.tariffs.list-page') }}">Тарифы</a>
                </li>
            @endif
        </ul>
    </li>
@endif
