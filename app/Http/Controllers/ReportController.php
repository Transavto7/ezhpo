<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Discount;
use App\Driver;
use App\Exports\ReportJournalExport;
use App\Point;
use App\Product;
use App\Req;
use App\Town;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function GetReport(Request $request)
    {

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
        $date_from = $data['date_from'] ?? Carbon::now()->startOfYear();
        $date_to = $data['date_to'] ?? Carbon::now();
        $date_from_time = $request->get('date_from_time', '00:00:00');
        $date_to_time = $request->get('date_from_time', '23:59:59');

        $pv_id = $data['pv_id'] ?? [0];

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

                    if ($isApi) {

                        $reports = Anketa::whereIn('pv_id', $pv_id)
                                         ->where('type_anketa', $request->get('type_anketa'))
                                         ->where('in_cart', 0)
                                         ->where(function ($q) use ($date_from, $date_to) {
                                             $q->where(function ($q) use ($date_from, $date_to) {
                                                 $q->whereNotNull('date')
                                                   ->whereBetween('date', [
                                                       $date_from.' '.'00:00:00',
                                                       $date_to.' '.'23:59:59',
                                                   ]);
                                             })->orWhere(function ($q) use ($date_from, $date_to) {
                                                 $q->whereNull('date')->whereBetween('period_pl', [
                                                     Carbon::parse($date_from)->format('Y-m'),
                                                     Carbon::parse($date_to)->format('Y-m'),
                                                 ]);
                                             });
                                         });

                        $reports2 = Anketa::whereIn('pv_id', $pv_id)
//                            ->where('type_anketa', 'medic')
                                          ->where('type_anketa', $request->get('type_anketa'))
                                          ->where('in_cart', 0)
                                          ->whereBetween("created_at", [
                                              $date_from." ".'00:00:00',
                                              $date_to." ".'23:59:59',
                                          ]);

                        if ($date_from_time && $date_to_time) {
                            $reports->whereTime('date', '>=', $date_from_time)
                                    ->whereTime('date', '<=', $date_to_time);

                            $reports2->whereTime('created_at', '>=', $date_from_time)
                                     ->whereTime('created_at', '<=', $date_to_time);
                        }

                        $reports = $reports->get();
                        $reports2 = $reports2->get();
//dd(
//    $reports->toArray(),
//    $reports2->toArray()
//);
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

    public function getDynamicTech(Request $request) {
        return $this->getDynamic($request, 'tech');
    }

    public function getDynamicMedic(Request $request) {
        return $this->getDynamic($request, 'medic');
    }

    public function getDynamicAll(Request $request) {
        return $this->getDynamic($request, 'all');
    }

    public function getDynamic(Request $request, $journal) {
        $monthTotalContainer = [];
        $monthNamesContainer = [];

        /** @var $pv_id string ID выбранных пунктов выпуска. Если их несколько, то они перечисляются через запятую. */
        $pv_id = trim($request->pv_id, ',[]');
        /** @var $town_id string ID выбранных городов пунктов выпуска. Если их несколько, то они перечисляются через запятую. */
        $town_id = trim($request->town_id, ',[]');
        /** @var $orderBy string Тип построения - execute (по дате осмотра) или created (по дате создания) */
        $orderBy = $request->order_by;

        $months = [];
        $periodStart = Carbon::now()->subMonths(11);
        $period = CarbonPeriod::create($periodStart, '1 month', Carbon::now());
        foreach ($period as $month) {
            $months[] = $month->format('F');
        }
        $months = array_reverse($months);

        if ($request->town_id || $request->pv_id) {
            /** @var $data_from Carbon Дата начала исчисления - это первый день месяца и -1 секунда... */
            $date_from = Carbon::now()->subMonths(11)->firstOfMonth()->startOfDay()->subSecond();
            /** @var $data_to Carbon Дата конца исчисления - это последний день + 1 день... */
            $date_to   = Carbon::now()->lastOfMonth()->addDay();
            // Почему такая разница?
            // Потому что конструкция between смотрит по правилу больше или меньше, но не больше/меньше ИЛИ РАВНО.
            // Если делать проверку по прямому математическому сравнению, это увеличит время обработки, а значит
            // запрос будет менее оптимизирован.
            $result    = [];
            $total     = [];

            // Достаём названия нужных нам месяцев для назначения по порядку.
            // Назначаем изначальную локаль как английскую, потом меняем обратно на русскую.
            Carbon::setLocale('en');
            for ($i = 0; $i < 12; $i++) {
                $monthNamesContainer[$i] = Carbon::now()->subMonths($i)->monthName;
                $monthTotalContainer[$i] = 0;
            }
            Carbon::setLocale('ru');

            $whereCase = "and ";
            $whereCase .= $journal == "all"
                ? "type_anketa in ('medic', 'tech')"
                : "type_anketa = '$journal'";

            $whereCase .= " and `in_cart` = 0 ";

            if ($pv_id) {
                if (str_contains($pv_id, ',')) {
                    $pointsNames = implode(',', Point::whereRaw("id in ($pv_id)")
                                                     ->pluck("name")
                                                     ->map(function ($pn) {
                                                         return "'$pn'";
                                                     }));
                    $whereCase .= " and `anketas`.`pv_id` in ($pointsNames) ";
                } else {
                    $pointName = trim(Point::find($pv_id)->name, "[]");
                    $whereCase .= " and `anketas`.`pv_id` = '$pointName' ";
                }
            } elseif ($town_id) {
                if (str_contains($town_id, ',')) {
                    $pointsNames = implode(',', Point::whereRaw("pv_id in ($town_id)")
                                                     ->pluck("name")
                                                     ->map(function ($tn) {
                                                         return "'$tn'";
                                                     }));
                    $whereCase .= " and `anketas`.`pv_id` in ($pointsNames) ";
                } else {
                    $pointName = trim(Point::where("pv_id", $town_id)->pluck('name'), "[]");
                    $whereCase .= " and `anketas`.`pv_id` = '$pointName' ";
                }
            }

            if ($orderBy == 'execute') {
                $mainField = "coalesce(date, period_pl)";
            } elseif ($orderBy == 'created') {
                $mainField = "created_at";
            }
            $mainTimeCondition = "($mainField between '$date_from' and '$date_to') ";
            $mainTimeResult = "date_format($mainField, '%Y-%m-%d')";

            $subSelectCase = "select sub.month as `month`,
                                     sub.cnt as `cnt`,
                                     sub.company_id as `company`,
                                     companies.name as `name`
                              from (
                                  select $mainTimeResult as `month`,
                                         company_id,
                                         count(*) as cnt
                                  from anketas
                                  where $mainTimeCondition
                                  $whereCase
                                  group by $mainTimeResult, company_id, type_anketa) sub
                                  left join companies on companies.hash_id = sub.company_id
                              ";
            $responseFromDB = DB::select($subSelectCase, ['$date_from' => $date_from, '$date_to' => $date_to]);

            foreach ($responseFromDB as $response) {
                $response = json_decode(json_encode($response), true);

                if (is_null($response['name']) || $response['name'] == '') {
                    continue;
                }

                Carbon::setLocale('en');
                //dd(Carbon::createFromFormat("d-m-Y", "01-$monthFromResponse-2000")->monthName);
                $monthName = ucfirst(Carbon::parse($response['month'])->monthName);
                Carbon::setLocale('ru');
                $result[$response["company"]]["name"] = $response['name'] ?? "Неизвестная компания";
                if (!isset($result[$response["company"]][$monthName])) {
                    $result[$response["company"]][$monthName] = 0;
                }
                $result[$response["company"]][$monthName] += $response["cnt"] ?? 0;
                $total[$monthName] = ($total[$monthName] ?? 0) + ($response["cnt"] ?? 0);
            }

            foreach ($monthNamesContainer as $monthIndex => $monthName) {
                $monthTotalContainer[$monthIndex] = $total[$monthName] ?? 0;
            }

            foreach ($monthNamesContainer as $monthIndex => &$monthName) {
                $carbon = Carbon::createFromFormat("F", $monthName);
                Carbon::setLocale('ru');
                $monthName = ucfirst($carbon->monthName);
                Carbon::setLocale('en');
            }
        }

        $towns = Town::get(['id', 'name']);
        $points = Point::get(['id', 'name', 'pv_id']);

        if (isset($total)) {
            $totalStr = "";
            foreach ($total as $sum) {
                $totalStr .= "$sum, ";
            }
            $totalStr = rtrim($totalStr, ",");
        }

        return view('reports.dynamic.medic.index', [
            'months' => $months,
            'companies' => $result ?? null,
            'total' => $total ?? null,
            'towns' => $towns,
            'points' => $points,
            'journal' => $journal,
            'totalstr' => $totalStr ?? '',
            'monthnames' => $monthNamesContainer,
            'monthtotal' => $monthTotalContainer
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
            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to, $products, $discounts),
            'techs_other' => $this->getJournalTechsOther($company, $date_from, $date_to, $products, $discounts),
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
            $driver_fio = $driver->where('driver_fio', '!=', null)->first();
            $result[$id]['driver_fio'] = $driver_fio ? $driver_fio->driver_fio : null;

            $result[$id]['pv_id'] = implode('; ', array_unique($driver->pluck('pv_id')->toArray()));

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
        $techs = Anketa::where('type_anketa', 'tech')
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
            $numberCar = $car->where('car_gos_number', '!=', null)->first();
            $typeCar = $car->where('type_auto', '!=', null)->first();
            $result[$id]['car_gos_number'] = $numberCar ? $numberCar->car_gos_number : null;
            $result[$id]['type_auto'] = $typeCar ? $typeCar->type_auto : null;
            $result[$id]['pv_id'] = implode('; ', array_unique($car->pluck('pv_id')->toArray()));

            foreach ($car->groupBy('type_view') as $rows) {
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

            $result[$id]['types']['is_dop']['total'] = $car->where('type_anketa', 'tech')
                                                           ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to, $products, $discounts) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
                         ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
                         ->where(function ($query) use ($company) {
                             $query->where('anketas.company_id', $company->hash_id)
                                   ->orWhere('anketas.company_name', $company->name);
                         })
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
                         ->select('driver_id', 'period_pl', 'type_view', 'driver_fio', 'date', 'is_dop', 'pv_id',
                                  'products_id', 'result_dop', 'type_anketa')
                         ->get();

        $result = [];

        foreach ($reports as $report) {
            try {
                if ($report->period_pl) {
                    $date = Carbon::parse($report->period_pl);
                } else {
                    $date = Carbon::parse($report->date);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;
            $result[$key]['reports'][$report->driver_id]['pv_id'] = implode('; ',
                                                                            array_unique($reports->where('driver_id', $report->driver_id)->pluck('pv_id')->toArray()));

            $total = $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] =
                ($result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total']?? 0) + 1;

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] =
                ($result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->driver_id]['types']['is_dop']['total'] =
                    ($result[$key]['reports'][$report->driver_id]['types']['is_dop']['total'] ?? 0) + 1;
            }

            if ($report->driver_id == null) {
                $services = explode(',', $company->products_id);
            } else {
                $services = explode(',', $report->products_id);
            }

            $types = explode('/', $report->type_view);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $disc = $discounts->where('products_id', $service->id);
                    $service->price = $service->price_unit;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'medic') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum'] = $service->price * $total;
                                } else {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum'] = $service->price;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['discount'] = $service->discount;
                                }
                            }
                        }
                    } else if (isset($result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa])) {
                        $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sync'] =
                            in_array($service->id, explode(',', $company->products_id));

                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum'] = $service->price * $total;
                        } else {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum'] = $service->price;
                        }

                        if ($service->discount) {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['discount'] = $service->discount;
                        }
                    }
                }
            }
        }

        return array_reverse($result);
    }

    public function getJournalTechsOther($company, $date_from, $date_to, $products, $discounts) {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa', 'pechat_pl'])
                         ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
                         ->where(function ($query) use ($company) {
                             $query->where('anketas.company_id', $company->hash_id)
                                   ->orWhere('anketas.company_name', $company->name);
                         })
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
                         ->select('anketas.car_gos_number', 'type_auto', 'period_pl', 'car_id', 'date', 'result_dop',
                                  'type_anketa', 'is_dop',
                                  'pv_id', 'products_id', 'type_view')
                         ->get();

        $result = [];

        foreach ($reports as $report) {
            try {
                if ($report->period_pl) {
                    $date = Carbon::parse($report->period_pl);
                } else {
                    $date = Carbon::parse($report->date);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->car_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->car_id]['type_auto'] = $report->type_auto;
            $result[$key]['reports'][$report->car_id]['pv_id'] = implode('; ',
                                                                         array_unique($reports->where('car_id', $report->car_id)->pluck('pv_id')->toArray()));

            $total = $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total']
                = ($result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->car_id]['types']['is_dop']['total']
                    = ($result[$key]['reports'][$report->car_id]['types']['is_dop']['total'] ?? 0) + 1;
            }

            if ($report->products_id == null) {
                $services = explode(',', $company->products_id);
            } else {
                $services = explode(',', $report->products_id);
            }

            $types = explode('/', $report->type_view);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $disc = $discounts->where('products_id', $service->id);
                    $service->price = $service->price_unit;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'tech') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum'] = $service->price * $total;
                                } else {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum'] = $service->price;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['discount'] = $service->discount;
                                }
                            }
                        }
                    }
                }
            }
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
