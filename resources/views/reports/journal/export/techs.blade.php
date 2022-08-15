<table>
    <thead>
        <tr>
            <th height="18" width="10" valign="center" align="center" colspan="8">
                <b>Техосмотры за период</b>
            </th>
        </tr>
        <tr>
            <th
                height="30" width="10" valign="center" align="center" style="background-color: #e6826a;"
            ><b>ID</b></th>

            <th
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Автомобиль</b></th>

            <th
                height="30" width="30" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Предрейсовый/Предсменный</b></th>

            <th
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Послерейсовый/Послесменный</b></th>

            <th
                height="30" width="20" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Несогласованные ПЛ</b></th>
        </tr>
    </thead>

    <tbody>
    @php
        $total_start = $total_end = $total_dop = 0;
        $sum_start = $sum_end = $sum_dop = 0;
    @endphp
    @foreach($techs as $id => $item)
        <tr>
            <td align="left">{{ $id }}</td>
            <td>

                @isset($item['car_gos_number'])
                    {{ $item['car_gos_number'] }}
                @endisset
                @isset($item['type_auto'])
                    ({{ $item['type_auto'] }})
                @endisset

            </td>
            <td align="center">
                @isset($item['types']['Предрейсовый/Предсменный']['total'])
                    @php $total_start += $item['types']['Предрейсовый/Предсменный']['total'] @endphp
                    {{ $item['types']['Предрейсовый/Предсменный']['total'] }}
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['Послерейсовый/Послесменный']['total'])
                    @php $total_end += $item['types']['Послерейсовый/Послесменный']['total'] @endphp
                    {{ $item['types']['Послерейсовый/Послесменный']['total'] }}
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['is_dop']['total'])
                    @php $total_dop += $item['types']['is_dop']['total'] @endphp
                    {{ $item['types']['is_dop']['total'] }}
                @endisset
            </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td align="center">
                @isset($item['types']['Предрейсовый/Предсменный']['sum'])
                    @php $sum_start += $item['types']['Предрейсовый/Предсменный']['sum'] @endphp
                    {{ $item['types']['Предрейсовый/Предсменный']['sum'] }}р
                @endisset
                @isset($item['types']['Предрейсовый/Предсменный']['discount'])
                    ({{ $item['types']['Предрейсовый/Предсменный']['discount'] }}%)
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['Послерейсовый/Послесменный']['sum'])
                    @php $sum_end +=  $item['types']['Послерейсовый/Послесменный']['sum'] @endphp
                    {{ $item['types']['Послерейсовый/Послесменный']['sum'] }}р
                @endisset
                @isset($item['types']['Послерейсовый/Послесменный']['discount'])
                    ({{ $item['types']['Послерейсовый/Послесменный']['discount'] }}%)
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['is_dop']['sum'])
                    @php $sum_dop += $item['types']['is_dop']['sum'] @endphp
                    {{ $item['types']['is_dop']['sum'] }}р
                @endisset
            </td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td>Всего:</td>
        <td align="center"> {{ $total_start }} </td>
        <td align="center"> {{ $total_end }} </td>
        <td align="center"> {{ $total_dop }} </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td align="center"> {{ $sum_start }} </td>
        <td align="center"> {{ $sum_end }} </td>
        <td align="center"> {{ $sum_dop }} </td>
    </tbody>
</table>
