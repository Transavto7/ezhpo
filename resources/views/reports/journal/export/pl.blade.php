<table>
    @foreach($data as $key => $item)
        <thead>
        <tr>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Месяц
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Автомобиль/Водитель
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Предрейсовый/Предсменный
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Послерейсовый/Послесменный
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Несогласованные ПЛ
            </th>

            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                БДД
            </th>
            <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                Отчёты с карт
            </th>
        </tr>
        </thead>
        @php
            $total_start = $total_end = $total_bdd = $total_cart = $total_pl = 0;
        @endphp

        <tbody>
        @foreach($item['reports'] as $report)
            <tr>
                <td>
                    {{ $months[$item['month']] }} {{ $item['year'] }}
                </td>
                <td>
                    @isset($report['driver_fio'])
                        {{ $report['driver_fio'] }}
                    @endisset
                    @isset($report['car_gos_number'])
                        {{ $report['car_gos_number'] }}
                    @endisset
                    @isset($report['type_auto'])
                        ({{ $report['type_auto'] }})
                    @endisset
                </td>
                <td align="center">
                    @isset($report['types']['Предрейсовый/Предсменный']['total'])
                        @php $total_start += $report['types']['Предрейсовый/Предсменный']['total'] @endphp
                        {{ $report['types']['Предрейсовый/Предсменный']['total'] }}
                    @endisset
                </td>
                <td align="center">
                    @isset($report['types']['Послерейсовый/Послесменный']['total'])
                        @php $total_end += $report['types']['Послерейсовый/Послесменный']['total'] @endphp
                        {{ $report['types']['Послерейсовый/Послесменный']['total'] }}
                    @endisset
                </td>
                <td align="center">
                    @isset($report['types']['bdd']['total'])
                        @php $total_bdd += $report['types']['bdd']['total'] @endphp
                        {{ $report['types']['bdd']['total'] }}
                    @endisset
                </td>
                <td align="center">
                    @isset($report['types']['report_cart']['total'])
                        @php $total_cart += $report['types']['report_cart']['total'] @endphp
                        {{ $report['types']['report_cart']['total'] }}
                    @endisset
                </td>
                <td align="center">
                    @isset($report['types']['pechat_pl']['total'])
                        @php $total_pl += $report['types']['pechat_pl']['total'] @endphp
                        {{ $report['types']['pechat_pl']['total'] }}
                    @endisset
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td align="center">
                <b>Всего</b>
            </td>
            <td align="center">{{ $total_start }}</td>
            <td align="center">{{ $total_end }}</td>
            <td align="center">{{ $total_bdd }} </td>
            <td align="center">{{ $total_cart }}</td>
            <td align="center">{{ $total_pl }}</td>
        </tr>
        </tbody>
    @endforeach
</table>
