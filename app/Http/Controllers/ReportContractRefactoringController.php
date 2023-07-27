<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Discount;
use App\Driver;
use App\Exports\ReportJournalExport;
use App\Models\Contract;
use App\Models\Service;
use App\Product;
use App\Req;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// без комментариев
class ReportContractRefactoringController extends Controller
{
    public $total_sum_for_contract = 0;
    public $contracts_ids;
    public $date_from;
    public $date_to;
    public $company;


    public $reports
        = [
            'journal' => 'Отчет по услугам компании[Договор]',
        ];


    public function index()
    {
        return view('reports.company-service-refactoring');
    }

    // поиск услуг для компании Отчет по услугам компании
    public function getContractsForCompany(Request $request)
    {
        $contracts = Company::with(['contracts'])
                            ->where('hash_id', $request->id)
                            ->get()
                            ->pluck('contracts')
                            ->flatten()
                            ->map(function ($q) {
                                return [
                                    'name' => $q->name,
                                    'id'   => $q->id,
                                ];
                            });

        return response($contracts);
    }

    public function getReport(Request $request)
    {
        $company = $request->company_id;

        if ($request->has('month')) {
            $date_from = Carbon::parse($request->month)->startOfMonth();
            $date_to = Carbon::parse($request->month)->endOfMonth();
        } else {
            $date_from = Carbon::parse($request->date_from)->startOfDay();
            $date_to = Carbon::parse($request->date_to)->endOfDay();
        }

        if ( !$company || !$date_to || !$date_from) {
            return response(null, 404);
        }


        $this->contracts_ids = $request->contracts_ids ?? [];
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->services  = Product::all();
        $this->discounts = Discount::all();
        $this->company = Company::with([
            'contracts.services',
            'contracts' => function ($q) {
                $q->whereIn('contracts.id', $this->contracts_ids);
            },
        ])->select('id', 'hash_id', 'name', 'products_id')
                          ->where('hash_id', $company)
                          ->first();

        if ( !Contract::groupBy('company_id')
                      ->where('company_id', $this->company->id)
                      ->select('company_id', DB::raw('SUM(main_for_company) AS count'))
                      ->whereDate('date_of_end', '>=', Carbon::parse($request->month)->endOfMonth())
                      ->whereDate('date_of_start', '<=', Carbon::parse($request->month)->startOfMonth())
                      ->having('count', '>', 0)
                      ->get(['company_id', 'count'])
                      ->count()) {
            $message = 'На данный период нет главного договора!';
        }

        return [
            'medics'       => $this->getJournalMedic(),
            'techs'        => $this->getJournalTechs(),
            'medics_other' => $this->getJournalMedicsOther(),
            'techs_other'  => $this->getJournalTechsOther(),
            'other'        => $this->getJournalOther(),
            'message'      => $message ?? '',
        ];
    }

    public function getJournalMedic()
    {
        $medics = Anketa::whereIn('type_anketa', [
            'medic',
            'bdd',
            'report_cart',
            'pechat_pl',
        ])->with([
                'driver.contracts.services',
                'company.contracts.services',
            ])
            ->where(function ($query) {
                $query->where('company_id', $this->company->hash_id);
            })
            ->where('in_cart', 0)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNotNull('date')
                      ->whereBetween('date', [
                          $this->date_from,
                          $this->date_to,
                      ]);
                })
                  ->orWhere(function ($q) {
                      $q->whereNull('date')->whereBetween('period_pl', [
                          $this->date_from->format('Y-m'),
                          $this->date_to->format('Y-m'),
                      ]);
                  });
            })
            ->get();

        $result = [];
        $total['drivers_count'] = 0;
        $total['price'] = 0;

        foreach ($medics as $inspection) {
            $driver_id = $inspection->driver->hash_id;
            if ($inspection->type_anketa === 'medic') {
                if (!isset($result[$driver_id]['types'][$inspection->type_view]['count'])) {
                    $result[$driver_id]['types'][$inspection->type_view]['count'] = 0;
                    $result[$driver_id]['types'][$inspection->type_view]['count_dop'] = 0;
                    $result[$driver_id]['types'][$inspection->type_view]['price'] = 0;
                }
                if (!isset($total['types'][$inspection->type_view]['count'])) {
                    $total['types'][$inspection->type_view]['count'] = 0;
                    $total['types'][$inspection->type_view]['count_dop'] = 0;
                    $total['types'][$inspection->type_view]['price'] = 0;
                }

                $result[$driver_id]['types'][$inspection->type_view]['count'] += 1;
                $total['types'][$inspection->type_view]['count'] += 1;
            } else {
                if (!isset($result[$driver_id]['types'][$inspection->type_anketa]['count'])) {
                    $result[$driver_id]['types'][$inspection->type_anketa]['count'] = 0;
                    $result[$driver_id]['types'][$inspection->type_anketa]['price'] = 0;
                }
                if (!isset($total['types'][$inspection->type_anketa]['count'])) {
                    $total['types'][$inspection->type_anketa]['count'] = 0;
                    $total['types'][$inspection->type_anketa]['price'] = 0;
                }

                if ($inspection->type_anketa === 'pechat_pl') {
                    $total['types'][$inspection->type_anketa]['count'] += $inspection->count_pl;
                    $result[$driver_id]['types'][$inspection->type_anketa]['count'] += $inspection->count_pl;
                } else {
                    $total['types'][$inspection->type_anketa]['count'] += 1;
                    $result[$driver_id]['types'][$inspection->type_anketa]['count'] += 1;
                }
            }

            if ($inspection->is_dop && $inspection->result_dop == null) {
                $result[$driver_id]['types'][$inspection->type_view]['count_dop'] += 1;
                $total['types'][$inspection->type_view]['count_dop'] += 1;
            }
        }

        foreach ($medics->groupBy('driver.hash_id') as $driver_id => $inspections) {
            $total['drivers_count'] += 1;
            $result[$driver_id]['pv_id'] = $inspections->pluck('pv_id')->unique()->implode('; ');

            if ($driver_id) {
                $result[$driver_id]['driver_fio'] = $inspections->first()->driver->fio;
                $services = $inspections->first()->driver->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->first();
            } else {
                $services = $this->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->where("main_for_company", 1)
                    ->first();
            }

            if (!$services) {
                continue;
            }

            $services = $services->services;

            foreach (['Предрейсовый/Предсменный', 'Послерейсовый/Послесменный'] as $type_view) {
                $count = $result[$driver_id]['types'][$type_view]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', 'medic')
                             ->filter(function ($item) use ($type_view) {
                                 return false !== stristr($item->type_view, $type_view);
                             }) as $service) {
                    $this->useDiscount($service, $count);

                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_view, $type_view) !== false) {
                        $result[$driver_id]['types'][$type_view]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$driver_id]['types'][$type_view]['price'] += $service->price;

                        if (!isset($total['types'][$type_view]['services'][$service->name])) {
                            $total['types'][$type_view]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_view]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_view]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_view]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }

            foreach (['report_cart', 'bdd', 'pechat_pl'] as $type_anketa) {
                $count = $result[$driver_id]['types'][$type_anketa]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', $type_anketa) as $service) {
                    $this->useDiscount($service, $count);

                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_anketa, $type_anketa) !== false) {
                        $result[$driver_id]['types'][$type_anketa]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$driver_id]['types'][$type_anketa]['price'] += $service->price;

                        if (!isset($total['types'][$type_anketa]['services'][$service->name])) {
                            $total['types'][$type_anketa]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_anketa]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_anketa]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_anketa]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }
        }

        return [
            'data' => $result,
            'total' => $total,
        ];
    }


    public function getJournalTechs()
    {
        $techs = Anketa::where('type_anketa', 'tech')
                ->with([
                    'car.contracts.services',
                    'company.contracts.services',
                ])
                ->where(function ($query) {
                    $query->where('anketas.company_id', $this->company->hash_id)
                          ->orWhere('anketas.company_name', $this->company->name);
                })
                ->where('anketas.in_cart', 0)
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereNotNull('anketas.date')
                          ->whereBetween('anketas.date', [
                              $this->date_from,
                              $this->date_to,
                          ]);
                    })
                      ->orWhere(function ($q) {
                          $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                              $this->date_from->format('Y-m'),
                              $this->date_to->format('Y-m'),
                          ]);
                      });
                })
                ->get();

        $result = [];
        $total = [];
        $total['cars_count'] = 0;
        $total['price'] = 0;

        foreach ($techs as $inspection) {
            $car_id = $inspection->car->hash_id;
            if ($inspection->type_anketa === 'tech') {
                if (!isset($result[$car_id]['types'][$inspection->type_view]['count'])) {
                    $result[$car_id]['types'][$inspection->type_view]['count'] = 0;
                    $result[$car_id]['types'][$inspection->type_view]['count_dop'] = 0;
                    $result[$car_id]['types'][$inspection->type_view]['price'] = 0;
                }
                if (!isset($total['types'][$inspection->type_view]['count'])) {
                    $total['types'][$inspection->type_view]['count'] = 0;
                    $total['types'][$inspection->type_view]['count_dop'] = 0;
                    $total['types'][$inspection->type_view]['price'] = 0;
                }

                $result[$car_id]['types'][$inspection->type_view]['count'] += 1;
                $total['types'][$inspection->type_view]['count'] += 1;
            } else {
                if (!isset($result[$car_id]['types'][$inspection->type_anketa]['count'])) {
                    $result[$car_id]['types'][$inspection->type_anketa]['count'] = 0;
                    $result[$car_id]['types'][$inspection->type_anketa]['price'] = 0;
                }
                if (!isset($total['types'][$inspection->type_anketa]['count'])) {
                    $total['types'][$inspection->type_anketa]['count'] = 0;
                    $total['types'][$inspection->type_anketa]['price'] = 0;
                }

                $total['types'][$inspection->type_anketa]['count'] += 1;
                $result[$car_id]['types'][$inspection->type_anketa]['count'] += 1;
            }

            if ($inspection->is_dop && $inspection->result_dop == null) {
                $result[$car_id]['types'][$inspection->type_view]['count_dop'] += 1;
                $total['types'][$inspection->type_view]['count_dop'] += 1;
            }
        }

        foreach ($techs->groupBy('car.hash_id') as $car_id => $inspections) {
            $total['cars_count'] += 1;
            $result[$car_id]['pv_id'] = $inspections->pluck('pv_id')->unique()->implode('; ');

            if ($car_id) {
                $result[$car_id]['car_gos_number'] = $inspections->first()->car->gos_number;
                $result[$car_id]['type_auto'] = $inspections->first()->car->type_auto;

                $services = $inspections->first()->car
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->first();
            } else {
                $services = $this->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->where("main_for_company", 1)
                    ->first();
            }

            if (!$services) {
                continue;
            }

            $services = $services->services;

            foreach (['Предрейсовый/Предсменный', 'Послерейсовый/Послесменный'] as $type_view) {
                $count = $result[$car_id]['types'][$type_view]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', 'tech')
                             ->filter(function ($item) use ($type_view) {
                                 return false !== stristr($item->type_view, $type_view);
                             }) as $service) {
                    $this->useDiscount($service, $count);
                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_view, $type_view) !== false) {
                        $result[$car_id]['types'][$type_view]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$car_id]['types'][$type_view]['price'] += $service->price;

                        if (!isset($total['types'][$type_view]['services'][$service->name])) {
                            $total['types'][$type_view]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_view]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_view]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_view]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }
        }

        return [
            'data' => $result,
            'total' => $total,
        ];
    }

    public function getJournalMedicsOther()
    {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
                 ->with([
                         'driver.contracts.services',
                         'company.contracts.services',
                 ])
                 ->where(function ($query) {
                     $query->where('anketas.company_id', $this->company->hash_id)
                           ->orWhere('anketas.company_name', $this->company->name);
                 })
                 ->where('in_cart', 0)
                 ->whereBetween('anketas.created_at', [
                     $this->date_from,
                     $this->date_to,
                 ])
                 ->where(function ($q) {
                     $q->where(function ($q) {
                         $q->whereNotNull('anketas.date')
                           ->whereNotBetween('anketas.date', [
                               $this->date_from,
                               $this->date_to,
                           ]);
                     })
                       ->orWhere(function ($q) {
                           $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                               $this->date_from->format('Y-m'),
                               $this->date_to->format('Y-m'),
                           ]);
                       });
                 })
                 ->get();

        $result = [];
        $total['drivers_count'] = 0;
        $total['price'] = 0;

        foreach ($reports->groupBy('driver.hash_id') as $driver_id => $inspections) {
            $total['drivers_count'] += 1;

            if ($driver_id) {
                $services = $inspections->first()->driver
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->first();
            } else {
                $services = $this->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '<', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->where("main_for_company", 1)
                    ->first();
            }

            if (!$services) {
                continue;
            }

            if (!$services) {
                continue;
            }

            $services = $services->services;

            foreach ($inspections as $report) {
                try {
                    if ($report->date) {
                        $date = Carbon::parse($report->date);
                    } else {
                        $date = Carbon::parse($report->period_pl);
                    }
                } catch (Exception $e) {
                    continue;
                }
                $key = $date->year . '-' . $date->month;
                $result[$key]['year'] = $date->year;
                $result[$key]['month'] = $date->month;
                $result[$key]['reports'][$driver_id]['pv_id'] = $inspections->pluck('pv_id')->unique()->implode('; ');
                if ($driver_id) {
                    $result[$key]['reports'][$driver_id]['driver_fio'] = $inspections->first()->driver->fio;
                }

                if (!isset($total['types'][$report->type_view]['count'])) {
                    $total['types'][$report->type_view]['count'] = 0;
                    $total['types'][$report->type_view]['count_dop'] = 0;
                }

                if (!isset($result[$key]['reports'][$driver_id]['types'][$report->type_view]['count'])) {
                    $result[$key]['reports'][$driver_id]['types'][$report->type_view]['count'] = 0;
                    $result[$key]['reports'][$driver_id]['types'][$report->type_view]['count_dop'] = 0;
                }

                $result[$key]['reports'][$driver_id]['types'][$report->type_view]['count'] += 1;
                $total['types'][$report->type_view]['count'] += 1;

                if ($report->is_dop && $report->result_dop == null) {
                    $result[$key]['reports'][$driver_id]['types'][$report->type_view]['count_dop'] += 1;
                    $total['types'][$report->type_view]['count_dop'] += 1;
                }
            }

            foreach (['Предрейсовый/Предсменный', 'Послерейсовый/Послесменный'] as $type_view) {
                $count = $result[$key][$driver_id]['types'][$type_view]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', 'medic')
                             ->filter(function ($item) use ($type_view) {
                                 return false !== stristr($item->type_view, $type_view);
                             }) as $service) {
                    $this->useDiscount($service, $count);

                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_view, $type_view) !== false) {
                        $result[$key][$driver_id]['types'][$type_view]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$key][$driver_id]['types'][$type_view]['price'] += $service->price;

                        if (!isset($total['types'][$type_view]['services'][$service->name])) {
                            $total['types'][$type_view]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_view]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_view]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_view]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }

            foreach (['report_cart', 'bdd', 'pechat_pl'] as $type_anketa) {
                $count = $result[$key][$driver_id]['types'][$type_anketa]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', $type_anketa) as $service) {
                    $this->useDiscount($service, $count);

                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_anketa, $type_anketa) !== false) {
                        $result[$key][$driver_id]['types'][$type_anketa]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$key][$driver_id]['types'][$type_anketa]['price'] += $service->price;

                        if (!isset($total['types'][$type_anketa]['services'][$service->name])) {
                            $total['types'][$type_anketa]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_anketa]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_anketa]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_anketa]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }
        }
        return [
            'data' => $result,
            'total' => $total
        ];
    }

    public function getJournalTechsOther()
    {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa', 'pechat_pl'])
             ->with([
                 'driver.contracts.services',
                 'company.contracts.services',
             ])
             ->where(function ($query) {
                 $query->where('anketas.company_id', $this->company->hash_id)
                       ->orWhere('anketas.company_name', $this->company->name);
             })
             ->where('in_cart', 0)
             ->whereBetween('anketas.created_at', [
                 $this->date_from,
                 $this->date_to,
             ])
             ->where(function ($q) {
                 $q->where(function ($q) {
                     $q->whereNotNull('anketas.date')
                       ->whereNotBetween('anketas.date', [
                           $this->date_from,
                           $this->date_to,
                       ]);
                 })
                   ->orWhere(function ($q) {
                       $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                           $this->date_from->format('Y-m'),
                           $this->date_to->format('Y-m'),
                       ]);
                   });
             })
             ->get();

        $result = [];
        $total['cars_count'] = 0;
        $total['price'] = 0;

        foreach ($reports->groupBy('car.hash_id') as $car_id => $inspections) {
            $total['cars_count'] += 1;

            if ($car_id) {
                $services = $inspections->first()->car
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->first();
            } else {
                $services = $this->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                    ->where('date_of_end', '>=', $this->date_to)
                    ->where('date_of_start', '<=', $this->date_from)
                    ->where("main_for_company", 1)
                    ->first();
            }

            if (!$services) {
                continue;
            }

            $services = $services->services;

            foreach ($inspections as $report) {
                try {
                    if ($report->date) {
                        $date = Carbon::parse($report->date);
                    } else {
                        $date = Carbon::parse($report->period_pl);
                    }
                } catch (Exception $e) {
                    continue;
                }
                $key = $date->year.'-'.$date->month; // key by date

                $result[$key]['year'] = $date->year;
                $result[$key]['month'] = $date->month;
                $result[$key]['reports'][$car_id]['car_gos_number'] = $report->car->gos_number;
                $result[$key]['reports'][$car_id]['type_auto'] = $report->car->type_auto;
                $result[$key]['reports'][$car_id]['pv_id'] = $inspections->pluck('pv_id')->unique()->implode('; ');

                if (!isset($total['types'][$report->type_view]['count'])) {
                    $total['types'][$report->type_view]['count'] = 0;
                    $total['types'][$report->type_view]['count_dop'] = 0;
                }

                if (!isset($result[$key]['reports'][$car_id]['types'][$report->type_view]['count'])) {
                    $result[$key]['reports'][$car_id]['types'][$report->type_view]['count'] = 0;
                    $result[$key]['reports'][$car_id]['types'][$report->type_view]['count_dop'] = 0;
                }

                $result[$key]['reports'][$car_id]['types'][$report->type_view]['count'] += 1;
                $total['types'][$report->type_view]['count'] += 1;

                if ($report->is_dop && $report->result_dop == null) {
                    $result[$key]['reports'][$car_id]['types'][$report->type_view]['count_dop'] += 1;
                    $total['types'][$report->type_view]['count_dop'] += 1;
                }
            }

            foreach (['Предрейсовый/Предсменный', 'Послерейсовый/Послесменный'] as $type_view) {
                $count = $result[$key][$car_id]['types'][$type_view]['count'] ?? 0;
                if ($count == 0) continue;

                foreach ($services->where('type_anketa', 'tech')
                             ->filter(function ($item) use ($type_view) {
                                 return false !== stristr($item->type_view, $type_view);
                             }) as $service) {
                    $this->useDiscount($service, $count);
                    if ($service->type_product === 'Разовые осмотры') {
                        $service->price = $service->price * $count;
                    }

                    if (strpos($service->type_view, $type_view) !== false) {
                        $result[$key][$car_id]['types'][$type_view]['services'][$service->name] = [
                            'price' => $service->price,
                            'discount' => $service->discount
                        ];
                        $result[$key][$car_id]['types'][$type_view]['price'] += $service->price;

                        if (!isset($total['types'][$type_view]['services'][$service->name])) {
                            $total['types'][$type_view]['services'][$service->name] = [
                                'count' => 0,
                                'price' => 0
                            ];
                        }

                        $total['types'][$type_view]['services'][$service->name]['count'] += $count;
                        $total['types'][$type_view]['services'][$service->name]['price'] += $service->price;
                        $total['types'][$type_view]['price'] += $service->price;
                        $total['price'] += $service->price;
                    }
                }
            }
        }

        return [
            'data' => $result,
            'total' => $total
        ];
    }

    public function getJournalOther()
    {
        $result = [];

        $companyServices = $this->company
            ->contracts
            ->pluck('services')
            ->flatten();

        $services = $this->services->where('type_product', 'Абонентская плата без реестров');

        $drivers = Driver::with(['contracts.services'])
                         ->where('company_id', $this->company->id)
                         ->get();

        $cars = Car::with(['contracts.services'])
                   ->where('company_id', $this->company->id)
                   ->get();

        foreach ($companyServices->where('essence', 0) as $service) {
            $result['company'][$service->name] = $service->pivot->service_cost;
        }

        foreach ($drivers as $driver) {
            $driverProdsID = $driver->contracts->whereIn('id', $this->contracts_ids)->pluck('services');
            foreach ($driverProdsID->whereIn('essence', [
                Product::ESSENCE_DRIVER,
                Product::ESSENCE_CAR_DRIVER,
            ]) as $service) {
                if ($service->type_product === 'Абонентская плата без реестров') {
                    $result['drivers'][] = [
                        'driver_fio' => $driver->fio,
                        'name' => $service->name,
                        'sum' => 1 * $service->pivot->service_cost,
                    ];
                }
            }
        }

        foreach ($cars as $car) {
            $carProdsID = $car->contracts->whereIn('id', $this->contracts_ids)->pluck('services');
            foreach ($carProdsID->whereIn('essence', [2, 3]) as $service) {
                if ($service->type_product === 'Абонентская плата без реестров') {
                    $result['cars'][] = [
                        'gos_number' => $car->gos_number,
                        'type_auto' => $car->type_auto,
                        'name' => $service->name,
                        'sum' => 1 * $service->pivot->service_cost,
                    ];
                }
            }
        }

        return $result;
    }

    public function ApiGetReport(Request $request)
    {
        $report = $this->GetReport($request);

        return response()->json($report);
    }

    public function useDiscount($service, $count) {
        $service->price = $service->pivot->service_cost;
        $service->discount = 0;
        $discountsForTech = $this->discounts->where('products_id', $service->id);
        if ($discountsForTech) {
            foreach ($discountsForTech as $discount) {
                $disSum = $discount->getDiscount($count);
                if ($disSum) {
                    $service->price = $service->price - ($service->price * $disSum / 100);
                    $service->discount = 1 * $disSum;
                }
            }
        }
    }
}
