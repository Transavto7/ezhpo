<h3 style="text-align: center">Отчет за {{ $action->getStartDate()->format('Y-m-d') }} - {{ $action->getEndDate()->format('Y-m-d') }}</h3>

<table>
    <thead>
    <tr>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">Название компании</th>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">1. Авторизация</th>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">2. Запрос журнала</th>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">3. Запрос отчета</th>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">4. Кол-во добавленных сущностей вод/авто через импорт</th>
        <th style="width: 150px; height: 50px; text-align: center; vertical-align: center; border: 5px solid black">5. Запрос документа</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $line)
        <tr>
            <td style="height: 30px; border: 5px solid black">{{ $line->getName() }}</td>
            <td style="height: 30px; border: 5px solid black">{{ $line->getAuthorization() }}</td>
            <td style="height: 30px; border: 5px solid black">{{ $line->getLogRequest() }}</td>
            <td style="height: 30px; border: 5px solid black">{{ $line->getReportRequest() }}</td>
            <td style="height: 30px; border: 5px solid black">{{ $line->getImport() }}</td>
            <td style="height: 30px; border: 5px solid black">{{ $line->getDocRequest() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
