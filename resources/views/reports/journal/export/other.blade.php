@isset($other['company'])
    <table>
        <thead>
            <tr>
                <th></th>
                <th height="18" valign="center" align="center" colspan="2">
                    Название услуги абонплата без реестров за выбранный период
                </th>
            </tr>
            <tr>
                <th></th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Услуга</b>
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Сумма</b>
                </th>
            </tr>
        </thead>

        <tbody>
        @php $total = 0; @endphp
        @foreach($other['company'] as $key => $sum)
            @php $total += $sum; @endphp
            <tr>
                <td></td>
                <td> {{ $key }} </td>
                <td align="center"> {{ $sum }} руб </td>
            </tr>
        @endforeach
            <tr>
                <td></td>
                <td> Всего: </td>
                <td align="center"> {{ $total }} руб </td>
            </tr>
        </tbody>
    </table>
@endisset

@isset($other['drivers'])
    <table>
        <thead>
        <tr>
            <th></th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Водитель</b>
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Услуга</b>
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Сумма</b>
            </th>
        </tr>
        </thead>

        <tbody>
        @php $total = 0; @endphp
        @foreach($other['drivers'] as $info)
            @php $total += $info['sum']; @endphp
            <tr>
                <td></td>
                <td> {{ $info['driver_fio'] }} </td>
                <td> {{ $info['name'] }} </td>
                <td align="center"> {{ $info['sum'] }} руб </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td> Всего: </td>
            <td></td>
            <td align="center"> {{ $total }} руб </td>
        </tr>
        </tbody>
    </table>
@endisset

@isset($other['cars'])
    <table>
        <thead>
        <tr>
            <th></th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Автомобиль</b>
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Услуга</b>
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Сумма</b>
            </th>
        </tr>
        </thead>

        <tbody>
        @php $total = 0; @endphp
        @foreach($other['cars'] as $info)
            @php $total += $info['sum']; @endphp
            <tr>
                <td></td>
                <td> {{ $info['gos_number'] }} ({{ $info['type_auto'] }}) </td>
                <td> {{ $info['name'] }} </td>
                <td align="center"> {{ $info['sum'] }} руб </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td> Всего: </td>
            <td></td>
            <td align="center"> {{ $total }} руб </td>
        </tr>
        </tbody>
    </table>
@endisset
