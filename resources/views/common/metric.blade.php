<style>
    .title {
        height: 50px;
        text-align: center;
        vertical-align: center;
        font-weight: bold;
        font-size: 16pt;
        overflow-wrap: break-word;
    }
    .header-cell {
        width: 150px;
        height: 50px;
        text-align: center;
        vertical-align: center;
        font-weight: bold;
        border: 5px solid black;
        overflow-wrap: break-word;
    }
    .body-cell {
        height: 30px;
        border: 5px solid black;
        overflow-wrap: break-word;
    }
</style>

<table>
    <thead>
    <tr>
        <th></th>
        <th style="height: 50px;text-align: center;vertical-align: center;font-weight: bold;font-size: 16pt;overflow-wrap: break-word;font-family: 'Times New Roman';" colspan="4">
            Отчет за {{ $action->getStartDate()->format('d.m.Y') }} - {{ $action->getEndDate()->format('d.m.Y') }}
        </th>
        <th></th>
    </tr>
    </thead>
</table>

<table>
    <thead>
    <tr>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">Название компании</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">1. Авторизация</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">2. Запрос журнала</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">3. Запрос отчета</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">4. Кол-во добавленных вод./авто через импорт</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">4. Кол-во добавленных вод./авто через форму</th>
        <th style="width: 150px;height: 65px;text-align: center;vertical-align: center;font-weight: bold;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">5. Запрос документа</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $line)
        <tr>
            <td style="height: 40px;border: 5px solid black;word-wrap: break-word;font-family: 'Times New Roman';">{{ $line->getName() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getAuthorization() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getLogRequest() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getReportRequest() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getDriverImport().' / '.$line->getCarImport() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getAddDriverViaForm().' / '.$line->getAddCarViaForm() }}</td>
            <td style="height: 40px;border: 5px solid black;vertical-align: center;font-family: 'Times New Roman';">{{ $line->getDocRequest() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
