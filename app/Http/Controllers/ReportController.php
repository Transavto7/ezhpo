<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Discount;
use App\Driver;
use App\Exports\ReportJournalExport;
use App\Product;
use App\Req;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function GetReport(Request $request)
    {
        if(auth()->user()->hasRole('medic') || auth()->user()->hasRole('tech')) {
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
            $company = Company::where('hash_id', $request->company_id)->select('id', 'hash_id', 'name')->first();
        }

        return view('reports.journal.index', [
            'company' => $company
        ]);
    }

    public function exportJournalData(Request $request) {
        return Excel::download(new ReportJournalExport($this->getJournalData($request)), 'export.xlsx');
    }

    public function getJournalData(Request $request) {
        $company = $request->company_id;

        if ($request->has('month')) {
            $date_from = Carbon::parse($request->month)->startOfMonth();
            $date_to = Carbon::parse($request->month)->endOfMonth();
        } else {
            $date_from = Carbon::parse($request->date_from)->startOfDay();
            $date_to = Carbon::parse($request->date_to)->endOfDay();
        }

        if (!$company || !$date_to || !$date_from) {
            return response(null, 404);
        }

        $company = Company::select('id', 'hash_id', 'name', 'products_id')->where('hash_id', $company)->first();
        $products = Product::all();
        $discounts = Discount::all();

        return [
            'medics' => $this->getJournalMedic($company, $date_from, $date_to, $products, $discounts),
            'techs' => $this->getJournalTechs($company, $date_from, $date_to, $products, $discounts),
            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to),
            'techs_other' => $this->getJournalTechsOther($company, $date_from, $date_to),
            'other' => $this->getJournalOther($company, $products),
        ];
    }

    public function getJournalMedic($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $medics = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
            ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                ->orWhere(function ($q) use ($date_from, $date_to) {
                    $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                        $date_from->format('Y-m'),
                        $date_to->format('Y-m'),
                    ]);
                });
            })
            ->select('driver_fio', 'driver_id', 'type_anketa', 'type_view', 'result_dop', 'products_id', 'pv_id',
                'is_dop')
            ->get();

        $result = [];

        foreach ($medics->groupBy('driver_id') as $driver) {
            $id = $driver->first()->driver_id;
            $result[$id]['driver_fio'] = $driver->first()->driver_fio;
            $result[$id]['pv_id'] = $driver->first()->pv_id;

            foreach ($driver->where('type_anketa', 'medic')->groupBy('type_view') as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                if ($id == null) {
                    $services = explode(',', $company->products_id);
                } else {
                    $services = explode(',', $driver->first()->products_id);
                }

                $types = explode('/', $type);
                $prods = $products->whereIn('id', $services)->where('type_anketa', 'medic');


                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$id]['types'][$type]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                $result[$id]['types'][$type]['name'] = $service->name;
                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$id]['types'][$type]['sum'] = $service->price * $total;
                                } else {
                                    $result[$id]['types'][$type]['sum'] = $service->price;
                                }
                            }
                        }
                    }
                }
            }


            foreach ($driver->groupBy('type_anketa') as $rows) {
                $type = $rows->first()->type_anketa;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $driver->first()->products_id);
                $prods = $products->whereIn('id', $services)->where('type_anketa', $type);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $result[$id]['types'][$type]['sync'] =
                            in_array($service->id, explode(',', $company->products_id));

                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$id]['types'][$type]['sum'] = $service->price * $total;
                        } else {
                            $result[$id]['types'][$type]['sum'] = $service->price;
                        }
                    }
                }
            }


            $result[$id]['types']['is_dop']['total'] = $driver->where('type_anketa', 'medic')
                ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalTechs($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $techs = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart', 'pechat_pl'])
            ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->select('car_gos_number', 'car_id', 'type_auto', 'type_anketa', 'is_dop', 'result_dop', 'pv_id',
                'type_view', 'products_id')
            ->get();

        $result = [];

        foreach ($techs->groupBy('car_id') as $car) {
            $id = $car->first()->car_id;
            $result[$id]['car_gos_number'] = $car->first()->car_gos_number;
            $result[$id]['type_auto'] = $car->first()->type_auto;
            $result[$id]['pv_id'] = $car->first()->pv_id;

            foreach ($car->where('type_anketa', 'tech')->groupBy('type_view') as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                if ($id == null) {
                    $services = explode(',', $company->products_id);
                } else {
                    $services = explode(',', $car->first()->products_id);
                }

                $types = explode('/', $type);
                $prods = $products->whereIn('id', $services)->where('type_anketa', 'tech');

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$id]['types'][$type]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$id]['types'][$type]['sum'] = $service->price * $total;
                                } else {
                                    $result[$id]['types'][$type]['sum'] = $service->price;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($car->groupBy('type_anketa') as $rows) {
                $type = $rows->first()->type_anketa;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $car->first()->products_id);
                $prods = $products->whereIn('id', $services)->where('type_anketa', $type);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $result[$id]['types'][$type]['sync'] =
                            in_array($service->id, explode(',', $company->products_id));

                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$id]['types'][$type]['sum'] = $service->price * $total;
                        } else {
                            $result[$id]['types'][$type]['sum'] = $service->price;
                        }
                    }
                }
            }


            $result[$id]['types']['is_dop']['total'] = $car->where('type_anketa', 'tech')
                ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('in_cart', 0)
            ->whereBetween('created_at', [
                $date_from,
                $date_to
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->select('driver_id', 'period_pl', 'type_view', 'driver_fio', 'date', 'is_dop', 'result_dop', 'type_anketa', 'pv_id')
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
            $result[$key]['reports'][$report->driver_id]['pv_id'] = $report->pv_id;
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
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa', 'pechat_pl'])
            ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->whereNotNull('car_id')
            ->where('in_cart', 0)
            ->whereBetween('anketas.created_at', [
                $date_from,
                $date_to
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->select('anketas.car_gos_number', 'type_auto', 'period_pl', 'car_id', 'date', 'result_dop', 'type_anketa',
                'pv_id', 'type_view')
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
            $result[$key]['reports'][$report->car_id]['pv_id'] = $report->pv_id;
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

    public function getJournalOther($company, $products) {
        $result = [];
        $companyProdsID = explode(',', $company->products_id);
        $prods = $products->where('type_product', 'Абонентская плата без реестров');
        $drivers = Driver::where('company_id', $company->id)->get();
        $cars = Car::where('company_id', $company->id)->get();

        foreach ($prods->whereIn('id', $companyProdsID)->where('essence', 0) as $product) {
            $result['company'][$product->name] = $product->price_unit;
        }

        foreach ($drivers as $driver) {
            $driverProdsID = explode(',', $driver->products_id);
            foreach ($prods->whereIn('id', $driverProdsID)->whereIn('essence', [1, 3]) as $product) {
                $result['drivers'][] = [
                    'driver_fio' => $driver->fio,
                    'name' => $product->name,
                    'sum' => 1 * $product->price_unit
                ];
            }
        }

        foreach ($cars as $car) {
            $carProdsID = explode(',', $car->products_id);
            foreach ($prods->whereIn('id', $carProdsID)->whereIn('essence', [2, 3]) as $product) {
                $result['cars'][] = [
                    'gos_number' => $car->gos_number,
                    'type_auto' => $car->type_auto,
                    'name' => $product->name,
                    'sum' => 1 * $product->price_unit
                ];
            }
        }

        return $result;
    }

    public function ApiGetReport(Request $request)
    {
        $report = $this->GetReport($request);

        return response()->json($report);
    }

}
