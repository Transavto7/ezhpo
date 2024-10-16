<table>
    <thead>
        <tr>
            <th height="18" width="10" valign="center" align="center" colspan="5">
                Техосмотры за период
            </th>
        </tr>
        <tr>
            <th height="30" width="10" valign="center" align="center" style="background-color: #e6826a;">
                <b>ID</b>
            </th>

            <th height="30" width="35" valign="center" align="center" style="background-color: #e6826a;">
                <b>Автомобиль</b>
            </th>

            <th height="30" width="30" valign="center" align="center" style="background-color: #e6826a;">
                <b>Предрейсовый/Предсменный</b>
            </th>

            <th height="30" width="35" valign="center" align="center" style="background-color: #e6826a;">
                <b>Послерейсовый/Послесменный</b>
            </th>

            <th height="30" width="20" valign="center" align="center" style="background-color: #e6826a;">
                <b>Несогласованные ПЛ</b>
            </th>
        </tr>
    </thead>

    <tbody>
    @php
        $total_start = $total_end = $total_dop = 0;
        $sum_start = $sum_end = $sum_dop = 0;
        $count = 0;
    @endphp
    @foreach($techs as $id => $item)
        @php $count++; @endphp

        <tr>
            <td align="left" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                {{ $id }}
            </td>

            <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                {{ $item['car_gos_number'] ?? 'Неизвестный автомобиль' }}
                @isset($item['type_auto'])
                    ({{ $item['type_auto'] }})
                @endisset

            </td>

            <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                @isset($item['types']['Предрейсовый/Предсменный']['total'])
                    @php $total_start += $item['types']['Предрейсовый/Предсменный']['total'] @endphp
                    {{ $item['types']['Предрейсовый/Предсменный']['total'] }} усл
                @endisset
            </td>

            <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                @isset($item['types']['Послерейсовый/Послесменный']['total'])
                    @php $total_end += $item['types']['Послерейсовый/Послесменный']['total'] @endphp
                    {{ $item['types']['Послерейсовый/Послесменный']['total'] }} усл
                @endisset
            </td>

            <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                @isset($item['types']['is_dop']['total'])
                    @php $total_dop += $item['types']['is_dop']['total'] @endphp
                    {{ $item['types']['is_dop']['total'] }} шт
                @endisset
            </td>
        </tr>

        <tr>
            <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif></td>
            <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                {{ $item['pv_id'] }}
            </td>

            <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                @isset($item['types']['Предрейсовый/Предсменный']['sum'])
                    @php $sum_start += $item['types']['Предрейсовый/Предсменный']['sum'] @endphp
                    {{ $item['types']['Предрейсовый/Предсменный']['sum'] }} руб
                @endisset
                @isset($item['types']['Предрейсовый/Предсменный']['discount'])
                    ({{ $item['types']['Предрейсовый/Предсменный']['discount'] }}%)
                @endisset
            </td>

            <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                @isset($item['types']['Послерейсовый/Послесменный']['sum'])
                    @php $sum_end +=  $item['types']['Послерейсовый/Послесменный']['sum'] @endphp
                    {{ $item['types']['Послерейсовый/Послесменный']['sum'] }} руб
                @endisset
                @isset($item['types']['Послерейсовый/Послесменный']['discount'])
                    ({{ $item['types']['Послерейсовый/Послесменный']['discount'] }}%)
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

        <td align="center"  @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
            {{ $sum_end }} руб
        </td>

        <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
    </tbody>
</table>
