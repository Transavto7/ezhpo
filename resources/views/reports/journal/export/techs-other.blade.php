<table>
    @foreach($data as $key => $item)
        <thead>
            <tr>
                <th height="18" width="10" valign="center" align="center" colspan="8">
                    <b>Техосмотры за другие периоды</b>
                </th>
            </tr>
            <tr>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    Месяц
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    Автомобиль
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
            </tr>
        </thead>
        @php
            $total_start = $total_end = $total_dop  = 0;
        @endphp

        <tbody>
            @foreach($item['reports'] as $report)
                <tr>
                    <td>
                        {{ $months[$item['month']-1] }} {{ $item['year'] }}
                    </td>
                    <td>
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
                        @isset($report['types']['is_dop']['total'])
                            @php $total_dop += $report['types']['is_dop']['total'] @endphp
                            {{ $report['types']['is_dop']['total'] }}
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
            <td align="center">{{ $total_dop }}</td>
        </tr>
        </tbody>
    @endforeach
</table>
