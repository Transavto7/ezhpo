<table>
    <thead>
        <tr>
            <th height="18" width="10" valign="center" align="center" colspan="8">
                <b>Медосмотры и другие услуги для водителей за выбранный период</b>
            </th>
        </tr>
        <tr>
            <th
                height="30" width="10" valign="center" align="center" style="background-color: #e6826a;"
            ><b>ID</b></th>

            <th
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Водитель</b></th>

            <th
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Предрейсовый/Предсменный</b></th>

            <th
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Послерейсовый/Послесменный</b></th>

            <th
                height="30" width="25" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Несогласованные ПЛ</b></th>

            <th
                height="30" width="25" valign="center" align="center" style="background-color: #e6826a;"
            ><b>БДД</b></th>

            <th
                height="30" width="25" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Отчёты с карт</b></th>

            <th
                height="30" width="25" valign="center" align="center" style="background-color: #e6826a;"
            ><b>Печать ПЛ</b></th>
        </tr>
    </thead>

    <tbody>
    @php
        $total_start = $total_end = $total_dop = $total_bdd = $total_cart = $total_pl = 0;
        $sum_start = $sum_end = $sum_dop = $sum_bdd = $sum_cart = $sum_pl = 0;
    @endphp
    @foreach($medics as $id => $item)
        <tr>
            <td align="left">{{ $id }}</td>
            <td>{{ $item['driver_fio'] }}</td>
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
            <td align="center">
                @isset($item['types']['bdd']['total'])
                    @php $total_bdd += $item['types']['bdd']['total'] @endphp
                    {{ $item['types']['bdd']['total'] }}
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['report_cart']['total'])
                    @php $total_cart += $item['types']['report_cart']['total'] @endphp
                    {{ $item['types']['report_cart']['total'] }}
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['pechat_pl']['total'])
                    @php $total_pl += $item['types']['pechat_pl']['total'] @endphp
                    {{ $item['types']['pechat_pl']['total'] }}
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
            <td align="center">
                @isset($item['types']['bdd']['sum'])
                    @php $sum_bdd += $item['types']['bdd']['sum'] @endphp
                    {{ $item['types']['bdd']['sum'] }}р
                @endisset
                @isset($item['types']['bdd']['discount'])
                    ({{ $item['types']['bdd']['discount'] }}%)
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['report_cart']['sum'])
                    @php $sum_cart += $item['types']['report_cart']['sum'] @endphp
                    {{ $item['types']['report_cart']['sum'] }}р
                @endisset
                @isset($item['types']['report_cart']['discount'])
                    ({{ $item['types']['report_cart']['discount'] }}%)
                @endisset
            </td>
            <td align="center">
                @isset($item['types']['pechat_pl']['sum'])
                    @php $sum_pl += $item['types']['pechat_pl']['sum'] @endphp
                    {{ $item['types']['pechat_pl']['sum'] }}р
                @endisset
                @isset($item['types']['pechat_pl']['discount'])
                    ({{ $item['types']['pechat_pl']['discount'] }}%)
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
        <td align="center"> {{ $total_bdd }} </td>
        <td align="center"> {{ $total_cart }} </td>
        <td align="center"> {{ $total_pl }} </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td align="center"> {{ $sum_start }} </td>
        <td align="center"> {{ $sum_end }} </td>
        <td align="center"> {{ $sum_dop }} </td>
        <td align="center"> {{ $sum_bdd }} </td>
        <td align="center"> {{ $sum_cart }} </td>
        <td align="center"> {{ $sum_pl }} </td>
    </tbody>
</table>
