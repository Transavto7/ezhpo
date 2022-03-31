<table id="reports-table-1" class="table table-striped table-sm">
    <thead>
    <tr>
        <th>Отчет по дате осмотра</th>
    </tr>
    <tr>
        <th>Водители</th>
        <th>Предрейсовые</th>
        <th>Послерейсовые</th>
        <th>БДД</th>
        <th>Отчёты с карт</th>
    </tr>
    </thead>
    <tbody>
    @if(count($reports) > 0)
        @foreach($reports->unique('driver_id') as $report)
            @php $predr = \App\Anketa::where('type_view', 'Предрейсовый')
->where('company_id', $company_id)
->where('in_cart', 0)
->whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $posler = \App\Anketa::where('type_view', 'Послерейсовый')
->where('company_id', $company_id)->where('in_cart', 0)
->whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $bdd = \App\Anketa::where('type_anketa', 'bdd')
->where('company_id', $company_id)->where('in_cart', 0)
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $report_cart = \App\Anketa::whereIn('type_anketa', ['report_cart'])
->where('company_id', $company_id)
->where('driver_id', $report->driver_id)
->where('in_cart', 0)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $syncFieldsDrivers = \App\Driver::getAutoSyncFields($report->driver_id); @endphp

            <tr>
                <td>
                    {{ $report->driver_fio }}

                    <div>
                        @if($syncFieldsDrivers)
                            @if(count($syncFieldsDrivers) > 0)
                                <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>
                            @endif
                        @endif

{{--                        @foreach(\App\Driver::getAutoSyncFields($report->driver_id) as $aSyncField)--}}
{{--                            <span class="text-bold text-success"><i class="fa fa-refresh"></i> {{ __($aSyncField) }}</span>--}}
{{--                        @endforeach--}}
                    </div>
                </td>
                <td>
                    {{ $predr }}

                    @if(request()->get('is_finance') && $predr > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Предрейсовый', $predr) !!}
                    @endif
                </td>
                <td>
                    {{ $posler }}

                    @if(request()->get('is_finance') && $posler > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Послерейсовый', $posler) !!}
                    @endif
                </td>
                <td>
                    {{ $bdd }}

                    @if(request()->get('is_finance') && $bdd > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'bdd', 'БДД', $bdd) !!}
                    @endif
                </td>
                <td>
                    {{ $report_cart }}

                    @if(request()->get('is_finance') && $report_cart > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'report_cart', 'Отчеты с карт', $report_cart) !!}
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<table id="reports-table-2" class="table table-striped table-sm">
    <thead>
    <tr>
        <th>Автомобили</th>
        <th>Предрейсовые</th>
        <th>Послерейсовые</th>
        <th>БДД</th>
        <th>Отчёты с карт</th>
    </tr>
    </thead>
    <tbody>
    @if(count($reports2) > 0)
        @foreach($reports2 as $report)
            @php $predr = \App\Anketa::where('type_view', 'Предрейсовый')
->where('in_cart', 0)
->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp
            @php $posler = \App\Anketa::where('type_view', 'Послерейсовый')
->where('in_cart', 0)->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $bdd = \App\Anketa::where('type_anketa', 'bdd')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $report_cart = \App\Anketa::whereIn('type_anketa', ['report_cart'])
->where('company_id', $company_id)
->where('in_cart', 0)
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $syncCarFields = \App\Car::getAutoSyncFields($report->car_id); @endphp

            <tr>
                <td>
                    {{ $report->car_gos_number }}

                    <div>
                        @if($syncCarFields)
                            @if(count($syncCarFields) > 0)
                                <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>
                            @endif
                        @endif

{{--                        @foreach($syncCarFields as $aSyncField)--}}
{{--                            <span class="text-bold text-success"><i class="fa fa-refresh"></i> {{ __($aSyncField) }}</span>--}}
{{--                        @endforeach--}}
                    </div>
                </td>
                <td>
                    {{ $predr }}

                    @if(request()->get('is_finance') && $predr > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Предрейсовый', $predr) !!}
                    @endif
                </td>
                <td>
                    {{ $posler }}

                    @if(request()->get('is_finance') && $posler > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Послерейсовый', $posler) !!}
                    @endif
                </td>

                <td>
                    {{ $bdd }}

                    @if(request()->get('is_finance') && $bdd > 0)
                        {!! \App\Driver::calcServices($report->car_id, 'bdd', 'БДД', $bdd) !!}
                    @endif
                </td>
                <td>
                    {{ $report_cart }}

                    @if(request()->get('is_finance') && $report_cart > 0)
                        {!! \App\Driver::calcServices($report->car_id, 'report_cart', 'Отчеты с карт', $report_cart) !!}
                    @endif
                </td>

            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<table id="reports-table-3" class="table table-striped table-sm">
    <thead>
    <tr>
        <th>Печать ПЛ</th>
    </tr>
    </thead>
    <tbody>
    <td>{{ \App\Anketa::where('type_anketa', 'pechat_pl')
            ->where('in_cart', 0)
            ->where('company_name', \App\Company::where('hash_id', $company_id)->first()->name)
            ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->count() }}</td>
    </tbody>
</table>

@isset($data['months'])
    @if(count($data['months']))
        <table id="reports-table-4" class="table table-responsive">
            <thead>
            <tr>
                @foreach($data['months'] as $month)
                    @if(!$month['hidden'])
                        <th style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="3">
                            {{ $month['name'] }} {{ $month['year'] }}
                        </th>
                    @endif
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data['months'] as $month)
                @if(!$month['hidden'])
                    <td colspan="3" class="p-0">
                        <table class="w-100 table">
                            <thead>
                                <th>Водитель</th>
                                <th>Предрейсовые</th>
                                <th>Послерейсовые</th>
                            </thead>

                            <tbody>
                            @foreach($month['reports'] as $report)
                                <tr>
                                    <td>{{ $report->driver_fio }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Предрейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послерейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                @endif
            @endforeach
            </tbody>
        </table>
    @endif
@else
    <p>Осмотры за другие месяцы не создавались</p>
@endisset
