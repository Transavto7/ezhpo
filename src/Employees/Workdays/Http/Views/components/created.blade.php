@php /** @var \Src\Employees\Workdays\Eloquent\Workday $workday */ @endphp
<div class="col-md-12">
    <div class="card p-2 text-xsmall">
        <b>Запись успешно создана!</b>
        <br/> ID записи: {{ $workday->id }}

        <br/>
        <b>Сотрудник: {{ $workday->employee->name }}</b>

        Тип осмотра:<b>{{ \Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum::create($workday->type_anketa)->getTitle() }}</b>

        <div>
            <i>Дата:
                <br/><b>{{ $workday->date->format('Y-m-d H:i') }}</b>
            </i>
        </div>

        <br/>
        Результат:<b>{{ $workday->admitted ? 'Допущен' : 'Не допущен' }}</b>
    </div>
</div>
