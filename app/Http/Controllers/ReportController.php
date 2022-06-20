<?php

namespace App\Http\Controllers;

use App\Anketa;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function GetApiReport (Request $request)
    {
        $data = $request->all();
        $model = $request->model;
        $id = $request->id;

        $date_from = isset($data['date_from']) ? $data['date_from'] : '';
        $date_to = isset($data['date_to']) ? $data['date_to'] : '';
        $driver_id = isset($data['driver_id']) ? $data['driver_id'] : '';
        $car_id = isset($data['car_id']) ? $data['car_id'] : '';
        $car_gos_number = isset($data['car_gos_number']) ? $data['car_gos_number'] : '';
        $company_id = isset($data['company_id']) ? $data['company_id'] : '';
        $month = isset($data['month']) ? $data['month'] : '';

        $date_field = 'date';

        $rData = [];

        switch($model) {

            case 'pechat_pl':
                return \App\Anketa::where('type_anketa', 'pechat_pl')
                    ->where('in_cart', 0)
                    ->where('company_name', \App\Company::where('hash_id', $company_id)->first()->name)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])
                    ->distinct('driver_id')
                    ->count();
                break;

            case 'Car':

                $rData['predr'] = \App\Anketa::where('type_view', 'Предрейсовый')
                    ->where('company_id', $company_id)
                    ->where('in_cart', 0)
                    ->where('type_anketa', 'tech')
                    ->where('car_gos_number', $car_gos_number)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['predr_sum'] = \App\Car::calcServices($car_id, 'tech', 'Предрейсовый', $rData['predr']);

                $rData['posler'] = \App\Anketa::where('type_view', 'Послерейсовый')
                    ->where('company_id', $company_id)
                    ->where('in_cart', 0)
                    ->where('type_anketa', 'tech')
                    ->where('car_gos_number', $car_gos_number)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['posler_sum'] = \App\Car::calcServices($car_id, 'tech', 'Послерейсовый', $rData['posler']);

                $rData['predsmenniy'] = \App\Anketa::where('type_view', 'Предсменный')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('type_anketa', 'tech')
                    ->where('car_gos_number', $car_gos_number)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['predsmenniy_sum'] = \App\Car::calcServices($car_id, 'tech', 'Предсменный', $rData['predsmenniy']);

                $rData['poslesmenniy'] = \App\Anketa::where('type_view', 'Послесменный')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('type_anketa', 'tech')
                    ->where('car_gos_number', $car_gos_number)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['poslesmenniy_sum'] = \App\Car::calcServices($car_id, 'tech', 'Послесменный', $rData['poslesmenniy']);

                $rData['bdd'] = \App\Anketa::where('type_anketa', 'bdd')
                    ->where('company_id', $company_id)
                    ->where('in_cart', 0)
                    ->where('car_gos_number', $car_gos_number)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['bdd_sum'] = \App\Car::calcServices($car_id, 'bdd', 'БДД', $rData['bdd']);

                $rData['report_cart'] = \App\Anketa::where('type_anketa', 'report_cart')
                    ->where('company_id', $company_id)
                    ->where('car_gos_number', $car_gos_number)
                    ->where('in_cart', 0)
                    ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count();
                $rData['report_cart_sum'] = \App\Car::calcServices($car_id, 'report_cart', 'Отчеты с карт', $rData['report_cart']);

                break;

            case 'Car_months':

                $rData['predr'] = \App\Anketa::where('type_view', 'Предрейсовый')
                    ->where('type_anketa', 'tech')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('car_id', $car_id)
                    ->whereMonth('date', $month)->count();

                $rData['posler'] = \App\Anketa::where('type_view', 'Послерейсовый')
                    ->where('type_anketa', 'tech')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('car_id', $car_id)
                    ->whereMonth('date', $month)->count();

                $rData['predsmenniy'] = \App\Anketa::where('type_view', 'Предсменный')
                    ->where('type_anketa', 'tech')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('car_id', $car_id)
                    ->whereMonth('date', $month)->count();

                $rData['poslesmenniy'] = \App\Anketa::where('type_view', 'Послесменный')
                    ->where('type_anketa', 'tech')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('car_id', $car_id)
                    ->whereMonth('date', $month)->count();

                $rData['bdd'] = \App\Anketa::where('type_anketa', 'bdd')
                    ->where('in_cart', 0)
                    ->where('company_id', $company_id)
                    ->where('car_id', $car_id)
                    ->whereMonth('created_at', $month)->count();

               $rData['report_cart'] = \App\Anketa::where('type_anketa', 'report_cart')
                   ->where('in_cart', 0)
                   ->where('company_id', $company_id)
                   ->where('car_id', $car_id)
                   ->whereMonth('date', $month)->count();

                break;

            case 'Driver_months':

                $rData['predr'] = \App\Anketa::where('type_view', 'Предрейсовый')
                ->where('type_anketa', 'medic')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                $rData['posler'] = \App\Anketa::where('type_view', 'Послерейсовый')
                ->where('type_anketa', 'medic')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                $rData['predsmenniy'] = \App\Anketa::where('type_view', 'Предсменный')
                ->where('type_anketa', 'medic')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                $rData['poslesmenniy'] = \App\Anketa::where('type_view', 'Послесменный')
                ->where('type_anketa', 'medic')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                $rData['bdd'] = \App\Anketa::where('type_anketa', 'bdd')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                $rData['report_cart'] = \App\Anketa::where('type_anketa', 'report_cart')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->whereMonth('date', $month)->count();

                break;

            case 'Dop':

                $rData['predr'] = \App\Anketa::where('type_view', 'Предрейсовый')
                    ->where('company_id', $company_id)
                    ->where('in_cart', 0)
                    ->where('is_dop', 1)
                    ->whereIn('type_anketa', ['medic', 'tech'])
                    ->where(function ($query) use ($month) {
                        return $query->whereMonth('date', $month)
                            ->orWhereMonth('period_pl', $month);
                    })
                    //->whereMonth('date', $month)
                    ->count();

                $rData['posler'] = \App\Anketa::where('type_view', 'Послерейсовый')
                    ->where('company_id', $company_id)
                    ->where('in_cart', 0)
                    ->where('is_dop', 1)
                    ->whereIn('type_anketa', ['medic', 'tech'])
                    ->where(function ($query) use ($month) {
                        return $query->whereMonth('date', $month)
                            ->orWhereMonth('period_pl', $month);
                    })
                    ->count();

                $rData['predsmenniy'] = \App\Anketa::where('type_view', 'Предсменный')
                    ->where('in_cart', 0)
                    ->where('is_dop', 1)
                    ->where('company_id', $company_id)
                    ->whereIn('type_anketa', ['medic', 'tech'])
                    ->where(function ($query) use ($month) {
                        return $query->whereMonth('date', $month)
                            ->orWhereMonth('period_pl', $month);
                    })
                    ->count();

                $rData['poslesmenniy'] = \App\Anketa::where('type_view', 'Послесменный')
                    ->where('in_cart', 0)
                    ->where('is_dop', 1)
                    ->where('company_id', $company_id)
                    ->whereIn('type_anketa', ['medic', 'tech'])
                    ->where(function ($query) use ($month) {
                        return $query->whereMonth('date', $month)
                            ->orWhereMonth('period_pl', $month);
                    })
                    ->count();

                break;

            case 'Driver':

                $rData['predr'] = \App\Anketa::where('type_view', 'Предрейсовый')
                ->where('company_id', $company_id)
                ->where('in_cart', 0)
                ->where('type_anketa', 'medic')
                ->where('driver_id', $driver_id)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['predr_sum'] = \App\Driver::calcServices($driver_id, 'medic', 'Предрейсовый', $rData['predr']);

                $rData['posler'] = \App\Anketa::where('type_view', 'Послерейсовый')
                ->where('company_id', $company_id)
                ->where('in_cart', 0)
                ->where('type_anketa', 'medic')
                ->where('driver_id', $driver_id)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['posler_sum'] = \App\Driver::calcServices($driver_id, 'medic', 'Послерейсовый', $rData['posler']);

                $rData['predsmenniy'] = \App\Anketa::where('type_view', 'Предсменный')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('type_anketa', 'medic')
                ->where('driver_id', $driver_id)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['predsmenniy_sum'] = \App\Driver::calcServices($driver_id, 'medic', 'Предсменный', $rData['predsmenniy']);

                $rData['poslesmenniy'] = \App\Anketa::where('type_view', 'Послесменный')
                ->where('in_cart', 0)
                ->where('company_id', $company_id)
                ->where('type_anketa', 'medic')
                ->where('driver_id', $driver_id)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['poslesmenniy_sum'] = \App\Driver::calcServices($driver_id, 'medic', 'Послесменный', $rData['poslesmenniy']);

                $rData['bdd'] = \App\Anketa::where('type_anketa', 'bdd')
                ->where('company_id', $company_id)
                ->where('in_cart', 0)
                ->where('driver_id', $driver_id)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['bdd_sum'] = \App\Driver::calcServices($driver_id, 'bdd', 'БДД', $rData['bdd']);

                $rData['report_cart'] = \App\Anketa::where('type_anketa', 'report_cart')
                ->where('company_id', $company_id)
                ->where('driver_id', $driver_id)
                ->where('in_cart', 0)
                ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ])->count();
                $rData['report_cart_sum'] = \App\Driver::calcServices($driver_id, 'report_cart', 'Отчеты с карт', $rData['report_cart']);

                break;
        }

        return $rData;
    }

    public function GetReport (Request $request)
    {
        if(auth()->user()->hasRole('medic', '==') || auth()->user()->hasRole('tech', '==')) {
            return back();
        }

        $data = $request->all();
        $isApi = isset($_GET['api']);
        $reports = [];
        $reports2 = [];
        $type_report = $request->type_report;
        $indexC = new IndexController();

        $company_fields = $indexC->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'hash_id';

        $pv_fields = $indexC->elements['Company']['fields']['pv_id'];
        $pv_fields['getFieldKey'] = 'name';
        $pv_fields['multiple'] = 1;

        $date_field = 'date';
        $date_from = isset($data['date_from']) ? $data['date_from'] : '';
        $date_to = isset($data['date_to']) ? $data['date_to'] : date('Y-m-d');
        $date_from_time = $request->get('date_from_time', '00:00:00');
        $date_to_time = $request->get('date_from_time', '23:59:59');

        $pv_id = isset($data['pv_id']) ? $data['pv_id'] : [0];

        $dopData = [];
        $hiddenMonths = 0;
        $hiddenMonthsTech = 0;
        $hiddenMonthsDop = 0;

        $reports = null;
        $reports2 = null;

        if(isset($data['filter'])) {
            $period_def = CarbonPeriod::create($date_from, $date_to)->month();
            $months_def = collect($period_def)->map(function (Carbon $date) {
                return $date->month;
            })->toArray();

            switch($type_report) {
                /**
                 * ГРАФИК РАБОТЫ ПВ
                 */
                case 'graph_pv':

                    if($isApi) {

                        $reports = Anketa::whereIn('pv_id', $pv_id)
                            ->where('type_anketa', 'medic')
                            ->where('in_cart', 0)
                            ->whereRaw("(date >= ? AND date <= ?)", [
                                $date_from . " " . '00:00:00',
                                $date_to . " " . '23:59:59'
                            ]);

                        $reports2 = Anketa::whereIn('pv_id', $pv_id)
                            ->where('type_anketa', 'medic')
                            ->where('in_cart', 0)
                            ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                                $date_from." ".'00:00:00',
                                $date_to." ".'23:59:59'
                            ]);

                        if($date_from_time && $date_to_time) {
                            $reports->whereTime('date', '>=', $date_from_time)
                                ->whereTime('date', '<=', $date_to_time);

                            $reports2->whereTime('created_at', '>=', $date_from_time)
                                ->whereTime('created_at', '<=', $date_to_time);
                        }

                        $reports = $reports->get();
                        $reports2 = $reports2->get();

                        return [
                            'reports' => $reports,
                            'reports2' => $reports2
                        ];
                    }

                    break;

                /**
                 * Отчет по услугам компании
                 */
                case 'journal':

                    if(isset($_GET['api'])) {
                        /**
                         * Таблица МЕДОСМОТРОВ
                         */
                        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
                            ->where('company_id', $data['company_id'])
                            ->where('in_cart', 0)
                            ->whereRaw("(date >= ? AND date <= ?)", [
                                $date_from." 00:00:00",
                                $date_to." 23:59:59"
                            ])
                            ->get()
                            ->unique('driver_id');

                        /**
                         * Нижняя таблица медосмотров
                         */
                        $reportsMedicCreatedAt = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
                            ->where('company_id', $data['company_id'])
                            ->where('in_cart', 0)
                            ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                                $date_from." 00:00:00",
                                $date_to." 23:59:59"
                            ])
                            ->get()
                            ->unique('driver_id');

                        if($reportsMedicCreatedAt) {
                            $dates = $reportsMedicCreatedAt->sortByDesc('date');

                            if(isset($dates->first()->date)) {
                                $date_to_period = $dates->first()->date;
                                $date_from_period = $dates->last()->date;

                                $period = CarbonPeriod::create($date_from_period, $date_to_period);

                                $months = collect($period)->map(function (Carbon $date) use ($months_def) {

                                    $dataMonth = [
                                        'month' => $date->month,
                                        'year' => $date->year,
                                        'name' => $date->monthName,
                                        'days' => $date->daysInMonth,
                                        'hidden' => in_array($date->month, $months_def)
                                    ];

                                    return $dataMonth;
                                })->unique('month')->toArray();

                                foreach($months as $monthKey => $month) {
                                    $reps = $reportsMedicCreatedAt->filter(function ($rep) use ($month, $months, $monthKey) {
                                        return \Carbon\Carbon::parse($rep->date)->month === $month['month'];
                                    })->unique('driver_id');

                                    $months[$monthKey]['hidden'] = $months[$monthKey]['hidden'] ? 1 : count($reps) <= 0;

                                    if($months[$monthKey]['hidden']) {
                                        $hiddenMonths += 1;
                                    }

                                    $months[$monthKey]['reports'] = $reps;
                                }

                                $dopData['months'] = $months;
                            }
                        }

                        /**
                         * ТАБЛИЦА ТЕХОСМОТРОВ
                         */
                        $reports2 = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
                            ->where('company_id', $data['company_id'])
                            ->where('in_cart', 0)
                            ->whereRaw("(date >= ? AND date <= ?)", [
                                $date_from." 00:00:00",
                                $date_to." 23:59:59"
                            ])
                            ->get()
                            ->unique('car_id');

                        /**
                         * Таблица ТЕХОСМОТРОВ - нижняя
                         */
                        $reports2TechCreatedAt = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
                            ->where('company_id', $data['company_id'])
                            ->where('in_cart', 0)
                            ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                                $date_from." 00:00:00",
                                $date_to." 23:59:59"
                            ])
                            ->get()
                            ->unique('car_id');

                        if($reports2TechCreatedAt) {
                            $datesTech = $reports2TechCreatedAt->sortByDesc('date');

                            if(isset($datesTech->first()->date)) {
                                $date_to_period = $datesTech->first()->date;
                                $date_from_period = $datesTech->last()->date;

                                $period = CarbonPeriod::create($date_from_period, $date_to_period);

                                $monthsTech = collect($period)->map(function (Carbon $date) use ($months_def) {

                                    $dataMonth = [
                                        'month' => $date->month,
                                        'year' => $date->year,
                                        'name' => $date->monthName,
                                        'days' => $date->daysInMonth,
                                        'hidden' => in_array($date->month, $months_def)
                                    ];

                                    return $dataMonth;
                                })->unique('month')->toArray();

                                foreach($monthsTech as $monthKey => $month) {
                                    $reps = $reports2TechCreatedAt->filter(function ($rep) use ($month, $monthsTech, $monthKey) {
                                        return \Carbon\Carbon::parse($rep->date)->month === $month['month'];
                                    })->unique('car_id');

                                    $monthsTech[$monthKey]['hidden'] = $monthsTech[$monthKey]['hidden'] ? 1 : count($reps) <= 0;

                                    if($monthsTech[$monthKey]['hidden']) {
                                        $hiddenMonthsTech += 1;
                                    }

                                    $monthsTech[$monthKey]['reports'] = $reps;
                                }

                                $dopData['monthsTech'] = $monthsTech;
                            }
                        }

                        /**
                         * Нижняя таблица РЕЖИМА ВВОДА ПЛ
                         */
                        $reportsDopCreatedAt = Anketa::whereIn('type_anketa', ['medic', 'tech'])
                            ->where('company_id', $data['company_id'])
                            ->where('in_cart', 0)
                            ->where('is_dop', 1)
                            ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                                $date_from." 00:00:00",
                                $date_to." 23:59:59"
                            ])
                            ->get();

                        if($reportsDopCreatedAt) {
                            $dates = $reportsDopCreatedAt->sortByDesc('date');

                            if(isset($dates->first()->date)) {
                                $date_to_period = $dates->first()->date;
                                $date_from_period = $dates->last()->date;

                                $period = CarbonPeriod::create($date_from_period, $date_to_period);

                                $months = collect($period)->map(function (Carbon $date) use ($months_def) {

                                    $dataMonth = [
                                        'month' => $date->month,
                                        'year' => $date->year,
                                        'name' => $date->monthName,
                                        'days' => $date->daysInMonth,
                                        'hidden' => in_array($date->month, $months_def)
                                    ];

                                    return $dataMonth;
                                })->unique('month')->toArray();

                                foreach($months as $monthKey => $month) {
                                    $reps = $reportsDopCreatedAt->filter(function ($rep) use ($month, $months, $monthKey) {
                                        return \Carbon\Carbon::parse($rep->date)->month === $month['month'];
                                    });

                                    $months[$monthKey]['hidden'] = $months[$monthKey]['hidden'] ? 1 : count($reps) <= 0;

                                    if($months[$monthKey]['hidden']) {
                                        $hiddenMonthsDop += 1;
                                    }

                                    $months[$monthKey]['reports'] = $reps;
                                }

                                $dopData['monthsDop'] = $months;
                            }
                        }

                        $dopData['reportsMedic'] = $reports;
                        $dopData['reportsTech'] = $reports2;
                        $dopData['hiddenMonths'] = $hiddenMonths;
                        $dopData['hiddenMonthsTech'] = $hiddenMonthsTech;
                        $dopData['hiddenMonthsDop'] = $hiddenMonthsDop;

                        return $dopData;
                    }

                    break;
            }
        }

        return view('pages.reports.all', [
            'title' => $this->reports[$type_report],
            'reports' => $reports,
            'reports2' => $reports2,
            'company_fields' => $company_fields, 'pv_fields' => $pv_fields,
            'type_report' => $type_report,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'date_field' => $date_field,
            'company_id' => isset($data['company_id']) ? $data['company_id'] : 0,
            'pv_id' => isset($data['pv_id']) ? $data['pv_id'] : 0,
            'data' => $dopData
        ]);
    }

    public function ApiGetReport (Request $request)
    {
        $report = $this->GetReport($request);

        return response()->json($report);
    }

}
