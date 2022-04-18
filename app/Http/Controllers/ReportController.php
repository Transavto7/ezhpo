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

    public function GetReport (Request $request)
    {
        if(auth()->user()->hasRole('medic', '==') || auth()->user()->hasRole('tech', '==')) {
            return back();
        }

        $data = $request->all();
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

        $pv_id = isset($data['pv_id']) ? $data['pv_id'] : [0];

        $dopData = [];
        $hiddenMonths = 0;
        $hiddenMonthsTech = 0;

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

                    $reports = Anketa::whereIn('pv_id', $pv_id)
                        ->where('type_anketa', 'medic')
                        ->where('in_cart', 0)
                        ->whereRaw("(date >= ? AND date <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->get();

                    $reports2 = Anketa::whereIn('pv_id', $pv_id)
                        ->where('type_anketa', 'medic')
                        ->where('in_cart', 0)
                        ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->get();

                    break;

                /**
                 * Отчет по услугам компании
                 */
                case 'journal':
                    $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
                        ->where('company_id', $data['company_id'])
                        ->where('in_cart', 0)
                        ->whereRaw("(date >= ? AND date <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])
                        ->get();

                    /*$reportsDate = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
                        ->where('company_id', $data['company_id'])
                        ->where('in_cart', 0)
                        ->whereRaw("(date >= ? AND date <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])
                        ->get();*/

                    if(true) {
                        $dates = $reports->sortByDesc('date');

                        if(isset($dates->first()->created_at)) {
                            $date_to_period = $dates->first()->created_at;
                            $date_from_period = $dates->last()->created_at;

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
                                $reps = $reports->filter(function ($rep) use ($month, $months, $monthKey) {
                                    return \Carbon\Carbon::parse($rep->created_at)->month === $month['month'];
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

                    $reports2 = Anketa::where('type_anketa', 'tech')
                        ->where('company_id', $data['company_id'])
                        ->where('in_cart', 0)
                        ->whereRaw("(date >= ? AND date <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])
                        ->get()
                        ->unique('car_gos_number');

                    if(true) {
                        $datesTech = $reports2->sortByDesc('date');

                        if(isset($datesTech->first()->created_at)) {
                            $date_to_period = $datesTech->first()->created_at;
                            $date_from_period = $datesTech->last()->created_at;

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
                                $reps = $reports->filter(function ($rep) use ($month, $monthsTech, $monthKey) {
                                    return \Carbon\Carbon::parse($rep->created_at)->month === $month['month'];
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
            'hiddenMonths' => $hiddenMonths,
            'hiddenMonthsTech' => $hiddenMonthsTech,
            'data' => $dopData
        ]);
    }

}
