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
                <a href="#">Нерабочие дни</a>
            </li>
        @endif
    </ul>
</li>
