<table>
    @foreach($data as $key => $item)
        <thead>
            <tr>
                <th height="18" valign="center" align="center" colspan="5">
                    Техосмотры за {{ $months[$item['month']-1] }} {{ $item['year'] }}
                </th>
            </tr>
            <tr>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>ID</b>
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Автомобиль</b>
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Предрейсовый/Предсменный</b>
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Послерейсовый/Послесменный</b>
                </th>
                <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                    <b>Несогласованные ПЛ</b>
                </th>
            </tr>
        </thead>

        @php
            $total_start = $total_end = $total_dop  = 0;
            $sum_start = $sum_end = 0;
            $count = 0;
        @endphp

        <tbody>
            @foreach($item['reports'] as $id => $report)
                @php $count++; @endphp

                <tr>
                    <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        {{ $id }}
                    </td>

                    <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['car_gos_number'])
                            {{ $report['car_gos_number'] }}
                        @endisset
                        @isset($report['type_auto'])
                            ({{ $report['type_auto'] }})
                        @endisset
                    </td>

                    <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['types']['Предрейсовый/Предсменный']['total'])
                            @php $total_start += $report['types']['Предрейсовый/Предсменный']['total'] @endphp
                            {{ $report['types']['Предрейсовый/Предсменный']['total'] }} усл
                        @endisset
                    </td>
                    <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['types']['Послерейсовый/Послесменный']['total'])
                            @php $total_end += $report['types']['Послерейсовый/Послесменный']['total'] @endphp
                            {{ $report['types']['Послерейсовый/Послесменный']['total'] }} усл
                        @endisset
                    </td>
                    <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['types']['is_dop']['total'])
                            @php $total_dop += $report['types']['is_dop']['total'] @endphp
                            {{ $report['types']['is_dop']['total'] }} шт
                        @endisset
                    </td>
                </tr>

                <tr>
                    <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif> </td>
                    <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        {{ $report['pv_id'] }}
                    </td>

                    <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['types']['Предрейсовый/Предсменный']['sum'])
                            @php $sum_start += $report['types']['Предрейсовый/Предсменный']['sum'] @endphp
                            {{ $report['types']['Предрейсовый/Предсменный']['sum'] }} руб
                        @endisset
                        @isset($report['types']['Предрейсовый/Предсменный']['discount'])
                            ({{ $report['types']['Предрейсовый/Предсменный']['discount'] }}%)
                        @endisset
                    </td>

                    <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                        @isset($report['types']['Послерейсовый/Послесменный']['sum'])
                            @php $sum_end +=  $report['types']['Послерейсовый/Послесменный']['sum'] @endphp
                            {{ $report['types']['Послерейсовый/Послесменный']['sum'] }} руб
                        @endisset
                        @isset($report['types']['Послерейсовый/Послесменный']['discount'])
                            ({{ $report['types']['Послерейсовый/Послесменный']['discount'] }}%)
                        @endisset
                    </td>

                    <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif></td>
                </tr>
            @endforeach

            <tr>
                <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
                <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    Всего:
                </td>
                <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    {{ $total_start }} усл
                </td>
                <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    {{ $total_end }} усл
                </td>
                <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    {{ $total_dop }} шт
                </td>
            </tr>

            <tr>
                <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
                <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>

                <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    {{ $sum_start }} руб
                </td>

                <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                    {{ $sum_end }} руб
                </td>

                <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
            </tr>
        </tbody>
    @endforeach
</table>
