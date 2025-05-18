@php
    $accessToReminders = user()->access(
        'reminders_read',
        'reminders_logs',
    )
@endphp

@if($accessToReminders)
    <li>
        <a href="#" data-btn-collapse="#reminders" role="button"><i class="fa fa-sticky-note"></i>Напоминания</a>
        <ul id="reminders" class="collapse list-unstyle">
            <li>
                @if(user()->access('reminders_read'))
                    <a href="{{ route('reminders.list-page') }}">Список напоминаний</a>
                @endif
                @if(user()->access('reminders_logs'))
                    <a href="{{ route('reminders.logs.list-page') }}">Журнал действий с напоминаниями</a>
                @endif
            </li>
        </ul>
    </li>
@endif
