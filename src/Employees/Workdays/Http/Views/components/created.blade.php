<div class="col-md-12">
    <div class="card p-2 text-xsmall">
        <b>Запись успешно создана!</b>
        <br/> ID записи: {{ $workday->id }}

        <br/>
        <b>Сотрудник: {{ $workday->employee->name }}</b>

        Тип осмотра:<b>{{ $workday->type_view }}</b>

        <div>
            <i>Дата:
                <br/><b>{{ $workday->date }}</b>
            </i>
        </div>

        <br/>
        Результат:<b>{{ $workday->admitted }}</b>
    </div>
</div>
