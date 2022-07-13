<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Company;
use App\Discount;
use App\Product;
use App\Req;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function GetReport(Request $request)
    {
        if(auth()->user()->hasRole('medic', '==') || auth()->user()->hasRole('tech', '==')) {
            return back();
        }

        $data = $request->all();
        $isApi = isset($_GET['api']);
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

    public function showJournal(Request $request) {
        $company = null;
        if ($request->has('company_id')) {
            $company = Company::where('hash_id', $request->company_id)->select('id', 'name')->first();
        }

        return view('reports.journal', [
            'company' => $company
        ]);
    }

    public function getJournalData(Request $request) {
        $company = $request->company_id;
        $date_to = $request->date_to;
        $date_from = $request->date_from;

        if (!$company || !$date_to || !$date_from) {
            return response(null, 404);
        }

        $products = Product::all();
        $discounts = Discount::all();
        return [
            'medics' => $this->getJournalMedic($company, $date_from, $date_to, $products, $discounts),
            'techs' => $this->getJournalTechs($company, $date_from, $date_to, $products, $discounts),
            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to),
            'techs_other' => $this->getJournalTechsOther($company, $date_from, $date_to),
            'other_pl' => $this->getJournalPl($company, $date_from, $date_to),
        ];
    }

    public function getJournalMedic($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $medics = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
            ->join('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where('anketas.company_id', $company)
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            Carbon::parse($date_from)->startOfDay(),
                            Carbon::parse($date_to)->endOfDay(),
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                            Carbon::parse($date_from)->format('Y-m'),
                            Carbon::parse($date_to)->format('Y-m'),
                        ]);
                    });
            })
            ->select('driver_fio', 'driver_id', 'type_anketa', 'type_view', 'result_dop',
                'is_dop', 'drivers.products_id')
            ->get();

        $result = [];

        foreach ($medics->groupBy('driver_id') as $driver) {
            $id = $driver->first()->driver_id;
            $result[$id]['driver_fio'] = $driver->first()->driver_fio;

            foreach ($driver->where('type_anketa', 'medic')->groupBy('type_view') as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $driver->first()->products_id);
                $prods = $products->whereIn('id', $services);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $discounts = $discounts->where('products_id', $service->id);

                        if($discounts->count()) {
                            foreach($discounts as $discount) {
                                $service->price_unit = $discount->add($total, $service->price_unit);
                            }
                        }
                    }

                    $result[$id]['types'][$type]['sum'] = $prods->sum('price_unit');
                }
            }

            foreach ($driver->groupBy('type_anketa') as $rows) {
                $type = $rows->first()->type_anketa;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $driver->first()->products_id);
                $prods = $products->whereIn('id', $services);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $discounts = $discounts->where('products_id', $service->id);

                        if($discounts->count()) {
                            foreach($discounts as $discount) {
                                $service->price_unit = $discount->add($total, $service->price_unit);
                            }
                        }
                    }

                    $result[$id]['types'][$type]['sum'] = $prods->sum('price_unit');
                }
            }


            $result[$id]['types']['is_dop']['total'] = $driver->where('type_anketa', 'medic')
                ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalTechs($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $techs = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
            ->join('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where('anketas.company_id', $company)
            ->whereNotNull('anketas.car_id')
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            Carbon::parse($date_from)->startOfDay(),
                            Carbon::parse($date_to)->endOfDay(),
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                            Carbon::parse($date_from)->format('Y-m'),
                            Carbon::parse($date_to)->format('Y-m'),
                        ]);
                    });
            })
            ->select('car_gos_number', 'car_id', 'type_auto', 'type_anketa', 'is_dop', 'result_dop',
                'type_view', 'cars.products_id')
            ->get();

        $result = [];

        foreach ($techs->groupBy('car_id') as $car) {
            $id = $car->first()->car_id;
            $result[$id]['car_gos_number'] = $car->first()->car_gos_number;
            $result[$id]['type_auto'] = $car->first()->type_auto;

            foreach ($car->where('type_anketa', 'tech')->groupBy('type_view') as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $car->first()->products_id);
                $prods = $products->whereIn('id', $services);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $discounts = $discounts->where('products_id', $service->id);

                        if($discounts->count()) {
                            foreach($discounts as $discount) {
                                $service->price_unit = $discount->add($total, $service->price_unit);
                            }
                        }
                    }

                    $result[$id]['types'][$type]['sum'] = $prods->sum('price_unit');
                }
            }

            foreach ($car->groupBy('type_anketa') as $rows) {
                $type = $rows->first()->type_anketa;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $car->first()->products_id);
                $prods = $products->whereIn('id', $services);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $discounts = $discounts->where('products_id', $service->id);

                        if($discounts->count()) {
                            foreach($discounts as $discount) {
                                $service->price_unit = $discount->add($total, $service->price_unit);
                            }
                        }
                    }

                    $result[$id]['types'][$type]['sum'] = $prods->sum('price_unit');
                }
            }


            $result[$id]['types']['is_dop']['total'] = $car->where('type_anketa', 'tech')
                ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
            ->where('company_id', $company)
            ->where('in_cart', 0)
            ->whereBetween('created_at', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            Carbon::parse($date_from)->startOfDay(),
                            Carbon::parse($date_to)->endOfDay(),
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            Carbon::parse($date_from)->format('Y-m'),
                            Carbon::parse($date_to)->format('Y-m'),
                        ]);
                    });
            })
            ->select('driver_id', 'period_pl', 'type_view', 'driver_fio', 'date', 'is_dop', 'result_dop', 'type_anketa')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            if ($report->period_pl) {
                $date = Carbon::parse($report->period_pl);
            } else {
                $date = Carbon::parse($report->date);
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;
            $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total']
                = $reports->where('driver_id', $report->driver_id)
                ->where('type_anketa', 'medic')->where('type_view', $report->type_view)->count();
            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total']
                = $reports->where('driver_id', $report->driver_id)->where('type_anketa', $report->type_anketa)->count();
            $result[$key]['reports'][$report->driver_id]['types']['is_dop']['total']
                = $reports->where('driver_id', $report->driver_id)->where('type_anketa', 'medic')
                    ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return array_reverse($result);
    }

    public function getJournalTechsOther($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa'])
            ->join('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where('anketas.company_id', $company)
            ->whereNotNull('car_id')
            ->where('in_cart', 0)
            ->whereBetween('anketas.created_at', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            Carbon::parse($date_from)->startOfDay(),
                            Carbon::parse($date_to)->endOfDay(),
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            Carbon::parse($date_from)->format('Y-m'),
                            Carbon::parse($date_to)->format('Y-m'),
                        ]);
                    });
            })
            ->select('anketas.car_gos_number', 'type_auto', 'period_pl', 'car_id', 'date', 'result_dop', 'type_anketa', 'type_view')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            if ($report->period_pl) {
                $date = Carbon::parse($report->period_pl);
            } else {
                $date = Carbon::parse($report->date);
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->car_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->car_id]['type_auto'] = $report->type_auto;
            $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total']
                = $reports->where('car_id', $report->car_id)
                    ->where('type_anketa', 'tech')->where('type_view', $report->type_view)->count();
            $result[$key]['reports'][$report->car_id]['types'][$report->type_anketa]['total']
                = $reports->where('car_id', $report->car_id)->where('type_anketa', $report->type_anketa)->count();
            $result[$key]['reports'][$report->car_id]['types']['is_dop']['total']
                = $reports->where('car_id', $report->car_id)->where('type_anketa', 'tech')
                    ->where('result_dop', null)->where('is_dop', 1)->count();

        }

        return array_reverse($result);
    }

    public function getJournalPl($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'tech'])
            ->where('company_id', $company)
            ->where('in_cart', 0)
            ->where('is_dop', 1)
            ->whereNull('result_dop')
            ->where(function($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->where(function ($q) use ($date_from, $date_to) {
                        $q->whereNotNull('anketas.date')
                            ->whereBetween('anketas.date', [
                                Carbon::parse($date_from)->startOfDay(),
                                Carbon::parse($date_to)->endOfDay(),
                            ]);
                    })
                        ->orWhere(function ($q) use ($date_from, $date_to) {
                            $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                                Carbon::parse($date_from)->format('Y-m'),
                                Carbon::parse($date_to)->format('Y-m'),
                            ]);
                        });
                })->orWhereBetween('created_at', [
                    $date_from." 00:00:00",
                    $date_to." 23:59:59"
                ]);
            })

            ->select('car_id', 'driver_id', 'car_gos_number', 'driver_fio', 'type_anketa',
                'period_pl', 'type_view')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            if (!$report->car_gos_number && !$report->driver_fio) {
                continue;
            }

            $date = Carbon::parse($report->period_pl);
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;

            $rps = $result[$key]['reports'][$report->driver_id];
            $view_count = 0;
            $anketa_count = 0;

            if (key_exists('types', $rps)) {
                if (key_exists($report->type_view, $rps['types'])
                    && key_exists('total', $rps['types'][$report->type_view])) {
                    $view_count = $rps['types'][$report->type_view]['total'];
                }

                if (key_exists($report->type_anketa, $rps['types'])
                    && key_exists('total', $rps['types'][$report->type_anketa])) {
                    $anketa_count = $rps['types'][$report->type_anketa]['total'];
                }
            }

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] = $view_count + 1;

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] = $anketa_count + 1;
        }

        return array_reverse($result);
    }

    public function ApiGetReport(Request $request)
    {
        $report = $this->GetReport($request);

        return response()->json($report);
    }

}
