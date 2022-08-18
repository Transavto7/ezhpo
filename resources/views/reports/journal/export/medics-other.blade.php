<table>
        @foreach($data as $key => $item)
            <thead>
                <tr>
                    <th height="18" width="10" valign="center" align="center" colspan="8">
                        Медосмотры за {{ $months[$item['month']-1] }} {{ $item['year'] }}
                    </th>
                </tr>

                <tr>
                    <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                        <b>ID</b>
                    </th>
                    <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                        <b>Водитель</b>
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

                    <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                        <b>БДД</b>
                    </th>
                    <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                        <b>Отчёты с карт</b>
                    </th>
                    <th height="30" valign="center" align="center" style="background-color: #e6826a;">
                        <b>Печать ПЛ</b>
                    </th>
                </tr>
            </thead>
            @php
                $total_start = $total_end = $total_dop = $total_bdd = $total_cart = $total_pl = 0;
                $sum_start = $sum_end =  $sum_bdd = $sum_cart = $sum_pl = 0;
                $count = 0;
            @endphp

            <tbody>
                @foreach($item['reports'] as $id => $report)
                    @php $count++; @endphp

                    <tr>
                        <td align="left" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            {{ $id }}
                        </td>

                        <td  @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            {{ $report['driver_fio'] }}
                        </td>

                        <td align="center"  @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['Предрейсовый/Предсменный']['total'])
                                @php $total_start += $report['types']['Предрейсовый/Предсменный']['total'] @endphp
                                {{ $report['types']['Предрейсовый/Предсменный']['total'] }} усл
                            @endisset
                        </td>

                        <td align="center"  @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['Послерейсовый/Послесменный']['total'])
                                @php $total_end += $report['types']['Послерейсовый/Послесменный']['total'] @endphp
                                {{ $report['types']['Послерейсовый/Послесменный']['total'] }} усл
                            @endisset
                        </td>

                        <td align="center"  @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['is_dop']['total'])
                                @php $total_dop += $report['types']['is_dop']['total'] @endphp
                                {{ $report['types']['is_dop']['total'] }} шт
                            @endisset
                        </td>

                        <td align="center"  @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['bdd']['total'])
                                @php $total_bdd += $report['types']['bdd']['total'] @endphp
                                {{ $report['types']['bdd']['total'] }} усл
                            @endisset
                        </td>

                        <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['report_cart']['total'])
                                @php $total_cart += $report['types']['report_cart']['total'] @endphp
                                {{ $report['types']['report_cart']['total'] }} усл
                            @endisset
                        </td>

                        <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['pechat_pl']['total'])
                                @php $total_pl += $report['types']['pechat_pl']['total'] @endphp
                                {{ $report['types']['pechat_pl']['total'] }} усл
                            @endisset
                        </td>
                    </tr>

                    <tr>
                        <td @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif></td>
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

                        <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['bdd']['sum'])
                                @php $sum_bdd += $report['types']['bdd']['sum'] @endphp
                                {{ $report['types']['bdd']['sum'] }} руб
                            @endisset
                            @isset($report['types']['bdd']['discount'])
                                ({{ $report['types']['bdd']['discount'] }}%)
                            @endisset
                        </td>

                        <td align="center"
                            @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif
                        >
                            @isset($report['types']['report_cart']['sum'])
                                @php $sum_cart += $report['types']['report_cart']['sum'] @endphp
                                {{ $report['types']['report_cart']['sum'] }} руб
                            @endisset
                            @isset($report['types']['report_cart']['discount'])
                                ({{ $report['types']['report_cart']['discount'] }}%)
                            @endisset
                        </td>

                        <td align="center" @if ($count % 2 == 0) style="background-color: #f2f2f2;" @endif>
                            @isset($report['types']['pechat_pl']['sum'])
                                @php $sum_pl += $report['types']['pechat_pl']['sum'] @endphp
                                {{ $report['types']['pechat_pl']['sum'] }} руб
                            @endisset
                            @isset($report['types']['pechat_pl']['discount'])
                                ({{ $report['types']['pechat_pl']['discount'] }}%)
                            @endisset
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td  @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif></td>
                    <td  @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
                        Всего:
                    </td>
                    <td align="center"  @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
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
                    <td align="center" @if ($count % 2 != 0) style="background-color: #f2f2f2;" @endif>
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
        @endforeach
</table>
