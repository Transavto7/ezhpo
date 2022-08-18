<table>
    <thead>
        <tr>
            <th height="18" width="10" valign="center" align="center" colspan="8">
                Медосмотры и другие услуги для водителей за выбранный период
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
                height="30" width="35" valign="center" align="center" style="background-color: #e6826a;"
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
            $sum_start = $sum_end =  $sum_bdd = $sum_cart = $sum_pl = 0;
            $count = 0;
        @endphp
        @foreach($medics as $id => $item)
            @php $count++; @endphp
            <tr>
                <td align="left" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    {{ $id }}
                </td>

                <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    {{ $item['driver_fio'] ?? 'Неизвестный водитель'}}
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

                <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    @isset($item['types']['bdd']['total'])
                        @php $total_bdd += $item['types']['bdd']['total'] @endphp
                        {{ $item['types']['bdd']['total'] }} усл
                    @endisset
                </td>

                <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    @isset($item['types']['report_cart']['total'])
                        @php $total_cart += $item['types']['report_cart']['total'] @endphp
                        {{ $item['types']['report_cart']['total'] }} усл
                    @endisset
                </td>

                <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    @isset($item['types']['pechat_pl']['total'])
                        @php $total_pl += $item['types']['pechat_pl']['total'] @endphp
                        {{ $item['types']['pechat_pl']['total'] }} усл
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

                <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    @isset($item['types']['bdd']['sum'])
                        @php $sum_bdd += $item['types']['bdd']['sum'] @endphp
                        {{ $item['types']['bdd']['sum'] }} руб
                    @endisset
                    @isset($item['types']['bdd']['discount'])
                        ({{ $item['types']['bdd']['discount'] }}%)
                    @endisset
                </td>

                <td align="center"
                    @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif
                >
                    @isset($item['types']['report_cart']['sum'])
                        @php $sum_cart += $item['types']['report_cart']['sum'] @endphp
                        {{ $item['types']['report_cart']['sum'] }} руб
                    @endisset
                    @isset($item['types']['report_cart']['discount'])
                        ({{ $item['types']['report_cart']['discount'] }}%)
                    @endisset
                </td>

                <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                    @isset($item['types']['pechat_pl']['sum'])
                        @php $sum_pl += $item['types']['pechat_pl']['sum'] @endphp
                        {{ $item['types']['pechat_pl']['sum'] }} руб
                    @endisset
                    @isset($item['types']['pechat_pl']['discount'])
                        ({{ $item['types']['pechat_pl']['discount'] }}%)
                    @endisset
                </td>
            </tr>
        @endforeach
        <tr>
            <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
            <td @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>Всего:</td>
            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_start }} усл
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_end }} усл
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_dop }} шт
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_bdd }} усл
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_cart }} усл
            </td>

            <td align="center"  @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $total_pl }} усл
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

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $sum_bdd }} руб
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $sum_cart }} руб
            </td>

            <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                {{ $sum_pl }} руб
            </td>
        </tr>
    </tbody>
</table>
