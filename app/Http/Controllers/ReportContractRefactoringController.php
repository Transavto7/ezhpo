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
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// без комментариев
class ReportContractRefactoringController extends Controller
{
    public $total_sum_for_contract = 0;


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
        $company             = $request->company_id;
        $this->contracts_ids = $request->contracts_ids ?? [];


        if ($request->has('month')) {
            $date_from = Carbon::parse($request->month)->startOfMonth();
            $date_to   = Carbon::parse($request->month)->endOfMonth();
        } else {
            $date_from = Carbon::parse($request->date_from)->startOfDay();
            $date_to   = Carbon::parse($request->date_to)->endOfDay();
        }

        if ( !$company || !$date_to || !$date_from) {
            return response(null, 404);
        }

        $company = Company::with([
            'contracts.services',
            'contracts' => function ($q) {
                $q->whereIn('contracts.id', $this->contracts_ids);
            },
        ])->select('id', 'hash_id', 'name', 'products_id')
                          ->where('hash_id', $company)
                          ->first();

        if ( !Contract::groupBy('company_id')
                      ->where('company_id', $company->id)
                      ->select('company_id', DB::raw('SUM(main_for_company) AS count'))
                      ->whereDate('date_of_end', '>=', Carbon::parse($request->month)->endOfMonth())
                      ->whereDate('date_of_start', '<=', Carbon::parse($request->month)->startOfMonth())
                      ->having('count', '>', 0)
                      ->get(['company_id', 'count'])
                      ->count()) {
            $message = 'На данный период нет главного договора!';
        }


        $services  = Service::all();
        $discounts = Discount::all();

        return [
            'medics'       => $this->getJournalMedic($company, $date_from, $date_to, $services, $discounts),
            'techs'        => $this->getJournalTechs($company, $date_from, $date_to, $services, $discounts),
            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to, $services, $discounts),
            'techs_other'  => $this->getJournalTechsOther($company, $date_from, $date_to, $services, $discounts),
            'other'        => $this->getJournalOther($company, $services),
            'message'      => $message ?? '',
        ];
    }

    public function getJournalMedic($company, $date_from, $date_to, $services, $discounts)
    {
        $medics = Anketa::whereIn('type_anketa', [
            'medic',
            'bdd',
            'report_cart',
            'pechat_pl',
        ])
                        ->with([
                            'driver.contracts.services',
                            'company.contracts.services',
                        ])
                        ->where(function ($query) use ($company) {
                            $query->where('company_id', $company->hash_id);
                        })
                        ->where('in_cart', 0)
                        ->where(function ($q) use ($date_from, $date_to) {
                            $q->where(function ($q) use ($date_from, $date_to) {
                                $q->whereNotNull('date')
                                  ->whereBetween('date', [
                                      $date_from,
                                      $date_to,
                                  ]);
                            })
                              ->orWhere(function ($q) use ($date_from, $date_to) {
                                  $q->whereNull('date')->whereBetween('period_pl', [
                                      $date_from->format('Y-m'),
                                      $date_to->format('Y-m'),
                                  ]);
                              });
                        })
                        ->get();

        $result = [];

        $type_views_eblan_mazaretto = [];
        $total_dop_ebat = 0;

        foreach ($medics as $medic) {
            if ($medic->driver->id) {
                if ($services = $medic->driver
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>=',
                                   ($medic->date
                                       ? Carbon::parse($medic->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $medic->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<=',
                                   ($medic->date
                                       ? Carbon::parse($medic->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $medic->period_pl)->startOfMonth())
                               )
                               ->first()) {

                    $services = $services->services;
                } else {
                    continue;
                }
            } else {
                if ($services = $medic->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>=',
                                   ($medic->date
                                       ? Carbon::parse($medic->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $medic->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<=',
                                   ($medic->date
                                       ? Carbon::parse($medic->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $medic->period_pl)->startOfMonth())
                               )
                               ->where("main_for_company", 1)
                               ->first()) {
                    $services = $services->services;
                } else {
                    continue;
                }
            }
            if($medic->type_anketa === 'medic'){
                if($result[$medic->driver->hash_id]['types'][$medic->type_view]['count'] ?? false){
                    $result[$medic->driver->hash_id]['types'][$medic->type_view]['count'] = $result[$medic->driver->hash_id]['types'][$medic->type_view]['count'] + 1;
                }else{
                    $result[$medic->driver->hash_id]['types'][$medic->type_view]['count'] = 1;
                }
            }else{
                if($result[$medic->driver->hash_id]['types'][$medic->type_anketa]['count'] ?? false){
                    $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['count'] = $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['count'] + 1;
                }else{
                    $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['count'] = 1;
                }
            }


            $flagEbat = false;
            if ( !($type_views_eblan_mazaretto[$medic->type_view.$medic->type_anketa.$medic->driver->hash_id] ??
                   false)) {
                $type_views_eblan_mazaretto[$medic->type_view.$medic->type_anketa.$medic->driver->hash_id]
                    = $medics->where('type_view', $medic->type_view)
                             ->where('driver_id', $medic->driver->hash_id)
                             ->count();
//                if($medic->driver_id == 139794){
//                    dd(
//                        $type_views_eblan_mazaretto,
//                        $medic->driver->hash_id,
//                        $medic->type_view.$medic->type_anketa.$medic->driver->hash_id
//                    );
//                }
            }
//            if($medic->driver_id == 139794){
//                dd(
//                    $type_views_eblan_mazaretto
//                );
//            }

            $total_for_type_view = $type_views_eblan_mazaretto[$medic->type_view.$medic->type_anketa
                                                               .$medic->driver->hash_id];

            if ($medic->is_dop && $medic->result_dop == null) {
                $result[$medic->driver->hash_id]['types']['is_dop']['total']
                    = ($result[$medic->driver->hash_id]['types']['is_dop']['total'] ?? 0) + 1;

                $total_dop_ebat++;
                $flagEbat = true;
//                dd(
//                    $medic->toArray()
//                );
            }

            $result[$medic->driver->hash_id]['driver_fio'] = $medic->driver->fio;
            if ( !($result[$medic->driver->hash_id]['pv_id'] ?? false)) {
                $result[$medic->driver->hash_id]['pv_id'] = $medics
                    ->where('driver_id', $medic->driver->hash_id)
                    ->pluck('pv_id')
                    ->unique()
                    ->implode('; ');
            }

            $disSum = 0;


            $services_fuck = collect();

            foreach ($services as $service) {
                $service->price = $service->pivot->service_cost;
                if ($medic->type_anketa !== $service->type_anketa) {
                    continue;
                }

                if ($discountsForTech = $discounts->where('products_id', $service->id)) {
                    foreach ($discountsForTech as $discount) {
                        $disSum = $discount->getDiscount($total_for_type_view);
                        if ($disSum) {
                            $service->price = $service->pivot->service_cost
                                              - ($service->pivot->service_cost
                                                 * $disSum
                                                 / 100);

                            $result[$medic->driver->hash_id]['types'][$medic->type_view]['discount'] = 1 * $disSum;
                        }
                    }
                }

                $vt           = $service->type_view;
                $type_explode = explode('/', $medic->type_view);


                if ($medic->type_anketa == 'medic') {
                    foreach ($type_explode as $mini_type) {
                        if (strpos($vt, $mini_type) !== false) {
                            if ($service->type_product === 'Разовые осмотры') {
                                $result[$medic->driver->hash_id]['types'][$medic->type_view]['sum'] = $service->pivot->service_cost *(1- ($disSum / 100)) * $total_for_type_view;
                            } else {
                                $result[$medic->driver->hash_id]['types'][$medic->type_view]['sum'] = $service->pivot->service_cost  *(1- ($disSum / 100));
                            }
                        }
                    }
                } else {
                    $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['sync']
                        = in_array($service->id, explode(',', $company->products_id));

                    if ($service->type_product === 'Разовые осмотры') {
                        $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['sum'] = $service->pivot->service_cost *(1- ($disSum / 100)) * $total_for_type_view;
                    } else {
                        $result[$medic->driver->hash_id]['types'][$medic->type_anketa]['sum'] = $service->pivot->service_cost *(1- ($disSum / 100));
                    }
                }

//                if($medic->type_view === 'Предрейсовый/Предсменный' && $medic->driver->hash_id){
//                    dd(
//                        $result,
//                        $service->pivot->service_cost,
//                        $disSum
//                    );
//                }

                if($flagEbat ?? false){
                    $result[$medic->driver->hash_id]['types']['is_dop']['services'][] = [
                        'sum'      => $result[$medic->driver->hash_id]['types'][($medic->type_anketa === 'medic') ? $medic->type_view : $medic->type_anketa]['sum'] ?? 0,
                        'price'      => $service->pivot->service_cost,
                        'discount' => $result[$medic->driver->hash_id]['types'][($medic->type_anketa === 'medic') ? $medic->type_view : $medic->type_anketa]['discount'] ?? 0,
                        'name'     => $service->name ?? '',
                        'id'       => $service->id ?? '',
                        'type_anketa'       => $service->type_anketa ?? '',
                        'type_view'       => $service->type_view ?? '',
                        'type_key'       => $service->type_anketa === 'medic' ? $medic->type_view : $medic->type_anketa,
                        'type_product'       => $service->type_product,
                    ];
                }else{
                    $result[$medic->driver->hash_id]['types'][($medic->type_anketa === 'medic') ? $medic->type_view : $medic->type_anketa]['services'][] = [
                        'sum'      => $result[$medic->driver->hash_id]['types'][($medic->type_anketa === 'medic') ? $medic->type_view : $medic->type_anketa]['sum'] ?? 0,
                        'discount' => $result[$medic->driver->hash_id]['types'][($medic->type_anketa === 'medic') ? $medic->type_view : $medic->type_anketa]['discount'] ?? 0,
                        'price'      => $service->pivot->service_cost,
                        'name'     => $service->name ?? '',
                        'id'       => $service->id ?? '',
                        'type_anketa'       => $service->type_anketa ?? '',
                        'type_view'       => $service->type_view ?? '',
                        'type_key'       => $service->type_anketa === 'medic' ? $medic->type_view : $medic->type_anketa,
                        'type_product'       => $service->type_product,
                    ];
                }
            }
        }
//dd(
//    $result
//);
        $service_counter = 0;
        $service_price = 0;

        $services_for_artem = collect();

        $temp_collection_service = collect();


        foreach ($result as $driver_id => $fcn_info){
            foreach (($fcn_info['types'] ?? []) as $type_key => $type_info){
                if($result[$driver_id]['types'][$type_key]['services'] ?? false){
                    $temp_collection_service->push($result[$driver_id]['types'][$type_key]['services']);
                    $result[$driver_id]['types'][$type_key]['services']
                        = collect($result[$driver_id]['types'][$type_key]['services'])
                        ->groupBy('id')
                        ->map(function ($group) use($type_key, &$services_for_artem) {
//dd(
//    $group->sum()
//);
//                            if($group->type_view === 'Предрейсовый/Предсменный'){
//                                dd(
//                                    $group,
//                                    $group
//                                );
//                            }
                            return [
                                'id'       => $group->first()['id'],
                                'name'     => $group->first()['name'],
                                'discount' => round($group->first()['discount'] ?? 0),
                                'price'    => $group->first()['price'] * (1 - ($group->first()['discount'] / 100)),
                                'count'    => $group->count(),

                                'type'        => $type_key,
                                'type_anketa' => $group->first()['type_anketa'],
                            ];
                        })
                        ->values();
                    if ($fist = $result[$driver_id]['types'][$type_key]['services']->first()) {
                        $service_counter += $result[$driver_id]['types'][$type_key]['count'] ?? 0 ;
                        $service_price += $fist['price'];
                    }
                }
            }
        }

//dd(
//    $temp_collection_service->toArray()
//);
        $temp_collection_service->flatten(1)
                                ->groupBy('type_key')
                                ->map(function ($group, $index) use($services_for_artem) {
//dd($group
//);
//                $group = $group;
                                    $services_for_artem->push([
                                        'id'       => $group->first()['id'],
                                        'name'     => $group->first()['name'],
                                        'discount' => round($group->first()['discount'] ?? 0),
                                        'price'    => $group->first()['price'] * (1 - ($group->first()['discount'] / 100)),
                                        'count'    => $group->count(),

                                        'type'        => $index,
                                        'type_product'        => $group->first()['type_product'],
                                    ]);

                                })
                                ->values();
//        dd(
//            $services_for_artem->toArray()
//        );

        $data = $result;
        $result = [];
        $result['data'] = $data;
        $result['services'] = [
            'count' => $service_counter,
            'price' => $service_price,
            'services_for_artem' => $services_for_artem
//                ->push([
//                    'count' => $total_dop_ebat,
//                    'type' => 'is_dop',
//                ])
                ->groupBy('type')
        ];
//        dd(
//            $result
//        );
//        dd(
//            $result
//        );
        return $result;
    }

    public function getJournalTechs($company, $date_from, $date_to, $products, $discounts)
    {
        $techs
                                    = Anketa::where('type_anketa', 'tech')
                                            ->with([
                                                'car.contracts.services',
                                                'company.contracts.services',
                                            ])
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
                                            ->get();
        $result = [];
        $total_dop_ebat = 0;
        $type_views_eblan_mazaretto = [];

        $services_fuck = collect();

        foreach ($techs as $tech) {
            if ($tech->car->id) {
                if ($services = $tech->car
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>',
                                   ($tech->date
                                       ? Carbon::parse($tech->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $tech->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<',
                                   ($tech->date
                                       ? Carbon::parse($tech->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $tech->period_pl)->startOfMonth())
                               )
                               ->first()) {

                    $services = $services->services;
                } else {
                    continue;
                }
            } else {
                if ($services = $tech->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>',
                                   ($tech->date
                                       ? Carbon::parse($tech->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $tech->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<',
                                   ($tech->date
                                       ? Carbon::parse($tech->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $tech->period_pl)->startOfMonth())
                               )
                               ->where("main_for_company", 1)
                               ->first()) {
                    $services = $services->services;
                } else {
                    continue;
                }
            }
            if($result[$tech->car->hash_id]['types'][$tech->type_view]['count'] ?? false){
                $result[$tech->car->hash_id]['types'][$tech->type_view]['count'] = $result[$tech->car->hash_id]['types'][$tech->type_view]['count'] + 1;
            }else{
                $result[$tech->car->hash_id]['types'][$tech->type_view]['count'] = 1;
            }

            if ($result[$tech->car->hash_id]['types'][$tech->type_view]['total'] ?? false) {
                $result[$tech->car->hash_id]['types'][$tech->type_view]['total'] += 1;
            } else {
                $result[$tech->car->hash_id]['types'][$tech->type_view]['total'] = 1;
            }



            // без комментариев
            if ( !($type_views_eblan_mazaretto[$tech->type_view.$tech->car->hash_id] ?? false)) {
                $total_for_type_view
                    = $type_views_eblan_mazaretto[$tech->type_view.$tech->car->hash_id]
                    = $techs->where(
                    'type_view', $tech->type_view
                )->where('car_id', $tech->car->hash_id)->count();
            }


            $result[$tech->car->hash_id]['car_gos_number'] = $tech->car->gos_number;
            $result[$tech->car->hash_id]['type_auto']      = $tech->car->type_auto;

            if ( !($result[$tech->car->hash_id]['pv_id'] ?? false)) {
                $result[$tech->car->hash_id]['pv_id'] = $techs
                    ->where('car_id', $tech->car->hash_id)
                    ->pluck('pv_id')
                    ->unique()
                    ->implode('; ');
            }




            foreach ($services as $service) {
                if ($tech->type_anketa !== $service->type_anketa) {
                    continue;
                }

                $service->price = $service->pivot->service_cost;

                if ($discountsForTech = $discounts->where('products_id', $service->id)) {
                    foreach ($discountsForTech as $discount) {
                        $disSum = $discount->getDiscount($total_for_type_view);
                        if ($disSum) {
                            $service->price                                                     = $service->pivot->service_cost
                                                                                                  - ($service->pivot->service_cost
                                                                                                     * $disSum / 100);
                            $result[$tech->car->hash_id]['types'][$tech->type_view]['discount'] = 1 * $disSum;
                        }
                    }
                }

                $vt           = $service->type_view;
                $type_explode = explode('/', $tech->type_view);

                foreach ($type_explode as $mini_type) {
                    if (strpos($vt, $mini_type) !== false) {
                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$tech->car->hash_id]['types'][$tech->type_view]['sum'] = $service->pivot->service_cost
                                                                                             * $total_for_type_view;
                        } else {
                            $result[$tech->car->hash_id]['types'][$tech->type_view]['sum'] = $service->pivot->service_cost;
                        }
                    }
                }
                $result[$tech->car->hash_id]['types'][$tech->type_view]['services'][] = [
                    'sum'      => $result[$tech->car->hash_id]['types'][$tech->type_view]['sum'] ?? 0,
                    'discount' => $result[$tech->car->hash_id]['types'][$tech->type_view]['discount'] ?? 0,
                    'name'     => $service->name ?? '',
                    'id'       => $service->id ?? '',
                    'type_key'       => $tech->type_view,
                    'type_product'       => $service->type_product,
                ];
            }


            $result[$tech->car->hash_id]['types']['is_dop']['total'] = $techs->where('type_anketa', 'tech')
                                                                             ->where('car_id', $tech->car->hash_id)
                                                                             ->where('result_dop', null)
                                                                             ->where('is_dop', 1)
                                                                             ->count();

            $total_dop_ebat += $result[$tech->car->hash_id]['types']['is_dop']['total'];

        }

        $service_counter = 0;
        $service_price = 0;
        $temp_collection_service = collect();
        $services_for_artem = collect();

        foreach ($result as $car_id => $fcn_info){
            foreach ($fcn_info['types'] as $type_key => $type_info){
                if($result[$car_id]['types'][$type_key]['services'] ?? false){
                    $temp_collection_service->push($result[$car_id]['types'][$type_key]['services']);
                    $result[$car_id]['types'][$type_key]['services'] = collect($result[$car_id]['types'][$type_key]['services'])
                        ->groupBy('id')
                        ->map(function ($group) use($type_key, &$services_for_artem) {

                            return [
                                'id'       => $group->first()['id'],
                                'name'     => $group->first()['name'],
                                'discount' => round($group->first()['discount']),
                                'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ?? 0)
                                                                                  / 100) - 1)),
                                'count'    => $group->count(),
                                'type' => $type_key,
                            ];
                        })
                        ->values();
                    if ($fist = $result[$car_id]['types'][$type_key]['services']->first()) {
                        $result[$car_id]['types'][$type_key]['count'] = $fist['count'];
                        $service_counter += $fist['count'];
                        $service_price += $fist['price'];
                    }
                }
            }
        }

        $temp_collection_service->flatten(1)
                                ->groupBy('type_key')
                                ->map(function ($group, $index) use($services_for_artem) {

                                    $services_for_artem->push([
                                        'id'       => $group->first()['id'],
                                        'name'     => $group->first()['name'],
                                        'discount' => round($group->first()['discount'] ?? 0),
                                        'price'    => (-(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ??
                                                                                                  0)
                                                                                           / 100) - 1))) * ($group->first()['type_product'] === 'Разовые осмотры' ? $group->count() : 1),
                                        'count'    => $group->count(),

                                        'type'        => $index,
                                    ]);
                                });
        $data = $result;
        $result = [];
        $result['data'] = $data;
        $result['services'] = [
            'count' => $service_counter,
            'price' => $service_price,
            'services_for_artem' => $services_for_artem
                ->push([
                    'count' => $total_dop_ebat,
                    'type' => 'is_dop',
                ])
                ->groupBy('type')
        ];

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to, $products, $discounts)
    {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
                         ->with([// 'services_snapshot',
                                 'driver.contracts.services',
                                 'company.contracts.services',
                         ])
                         ->where(function ($query) use ($company) {
                             $query->where('anketas.company_id', $company->hash_id)
                                   ->orWhere('anketas.company_name', $company->name);
                         })
                         ->where('in_cart', 0)
                         ->whereBetween('anketas.created_at', [
                             $date_from,
                             $date_to,
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
                         ->get();

        $result = [];
        $total_dop_ebat = 0;


        $companyProdsID = $company
            ->contracts
            ->pluck('services')
            ->flatten()
            ->pluck('id')
            ->toArray();

        foreach ($reports as $report) {
//            if($report->driver->contracts->whereIn('id', $this->contracts_ids)->isEmpty()){
//                continue;
//            }
            if($report->driver->id){
                if ($services = $report->driver
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '<',
                                   ($report->date
                                       ? Carbon::parse($report->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '>',
                                   ($report->date
                                       ? Carbon::parse($report->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->first()) {
                    $services = $services->services;
                }else{
                    continue;
                }
            } else {
                if ($services = $report->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '<',
                                   ($report->date ??
                                    Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '>',
                                   ($report->date ??
                                    Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where("main_for_company", 1)
                               ->first()) {
                    $services = $services->services;
                } else {
                    continue;
//                    $services = collect();
                }
            }

            $flagEbat = false;
            try {
                if ($report->period_pl) {
                    $date = Carbon::parse($report->period_pl);
                } else {
                    $date = Carbon::parse($report->date);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year.'-'.$date->month; // key by date

            $result[$key]['year']                                      = $date->year;
            $result[$key]['month']                                     = $date->month;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver->fio;
            $result[$key]['reports'][$report->driver_id]['pv_id']      = implode('; ',
                array_unique($reports->where('driver_id', $report->driver_id)->pluck('pv_id')->toArray()));

            $total
                = $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total']
                = ($result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] ?? 0) + 1;

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total']
                = ($result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->driver_id]['types']['is_dop']['total']
                    = ($result[$key]['reports'][$report->driver_id]['types']['is_dop']['total'] ?? 0) + 1;
                $total_dop_ebat++;
                $flagEbat = true;
            }


            $types = explode('/', $report->type_view);

            if ($services->count() > 0) {
                foreach ($services as $service) {
                    if ($report->type_anketa !== $service->type_anketa) {
                        continue;
                    }

                    $disc              = $discounts->where('products_id', $service->id);
                    $service->price    = $service->pivot->service_cost;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price    = $service->pivot->service_cost - ($service->pivot->service_cost
                                                                                      * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'medic') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sync']
                                    = in_array($service->id, $companyProdsID);

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum']
                                        = $service->pivot->service_cost * $total;
                                } else {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum']
                                        = $service->pivot->service_cost;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['discount']
                                        = $service->discount;
                                }
                                if($flagEbat?? false){
                                    $result[$key]['reports'][$report->driver_id]['types']['is_dop']['services'][] = [
                                        'sum'      => $result[$report->driver_id]['types'][$report->type_view]['sum'] ?? 0,
                                        'discount' => $result[$report->driver_id]['types'][$report->type_view]['discount'] ?? 0,
                                        'name'     => $service->name ?? '',
                                        'id'       => $service->id ?? '',
                                        'type_key'       => $report->type_view,
                                    ];
                                }else{
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['services'][] = [
                                        'sum'      => $result[$report->driver_id]['types'][$report->type_view]['sum'] ?? 0,
                                        'discount' => $result[$report->driver_id]['types'][$report->type_view]['discount'] ?? 0,
                                        'name'     => $service->name ?? '',
                                        'id'       => $service->id ?? '',
                                        'type_key'       => $report->type_view,
                                    ];
                                }
                            }
                        }
                    } else {
                        if (isset($result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa])) {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sync']
                                = in_array($service->id, $companyProdsID);

                            if ($service->type_product === 'Разовые осмотры') {
                                $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum']
                                    = $service->pivot->service_cost * $total;
                            } else {
                                $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum']
                                    = $service->pivot->service_cost;
                            }

                            if ($service->discount) {
                                $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['discount']
                                    = $service->discount;
                            }
                            if($flagEbat?? false){
                                $result[$key]['reports'][$report->driver_id]['types']['is_dop']['services'][] = [
                                    'sum'      => $result[$report->driver_id]['types'][$report->type_anketa]['sum'] ?? 0,
                                    'discount' => $result[$report->driver_id]['types'][$report->type_anketa]['discount'] ?? 0,
                                    'name'     => $service->name ?? '',
                                    'type_key'       => $report->type_anketa,
                                    'id'       => $service->id ?? ''
                                ];
                            }else{
                                $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['services'][] = [
                                    'sum'      => $result[$report->driver_id]['types'][$report->type_anketa]['sum'] ?? 0,
                                    'discount' => $result[$report->driver_id]['types'][$report->type_anketa]['discount'] ?? 0,
                                    'name'     => $service->name ?? '',
                                    'type_key'       => $report->type_anketa,
                                    'id'       => $service->id ?? ''
                                ];
                            }
                        }
                    }

                }

//                    $result[$key]['reports'][$report->driver_id]['types'][($report->type_anketa === 'medic') ? $report->type_view : $report->type_anketa]['s0 = [
//                        'sum'      => $result[$report->driver->hash_id]['types'][($report->type_anketa === 'medic') ? $report->type_view : $report->type_anketa]['sum'] ?? '',
//                        'discount' => $result[$report->driver->hash_id]['types'][($report->type_anketa === 'medic') ? $report->type_view : $report->type_anketa]['discount'] ?? '',
//                        'name'     => $service->name ?? '',
//                        'id'       => $service->id ?? '',
//                    ];
//                }
            }
        }

        $service_counter = 0;
        $service_price = 0;
        $temp_collection_service = collect();
        $services_for_artem = collect();
        foreach ($result as $key => $period_info){
            foreach ($period_info['reports'] as $driver_id => $fcn_info){
                foreach ($fcn_info['types'] as $type_key => $type_info){
                    if($result[$key]['reports'][$driver_id]['types'][$type_key]['services'] ?? false){
                        $temp_collection_service->push($result[$key]['reports'][$driver_id]['types'][$type_key]['services']);
                        $result[$key]['reports'][$driver_id]['types'][$type_key]['services'] = collect($result[$key]['reports'][$driver_id]['types'][$type_key]['services'])
                            ->groupBy('id')
                            ->map(function ($group) use($type_key, &$services_for_artem){
//                                $services_for_artem->push([
//                                    'id'       => $group->first()['id'],
//                                    'name'     => $group->first()['name'],
//                                    'discount' => round($group->first()['discount'] ?? 0),
//                                    'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ?? 0)
//                                                                                      / 100) - 1)),
//                                    'count'    => $group->count(),
//
//                                    'type' => $type_key
//                                ]);
                                return [
                                    'id'       => $group->first()['id'],
                                    'name'     => $group->first()['name'],
                                    'discount' => $group->first()['discount'],
                                    'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ?? 0)
                                                                                      / 100) - 1)),
                                    'count'    => $group->count(),
                                    'type' => $type_key
                                ];
                            })
                            ->values();
                        if ($fist = $result[$key]['reports'][$driver_id]['types'][$type_key]['services']->first()) {
                            $result[$driver_id]['types'][$type_key]['count'] = $fist['count'];
                            $service_counter += $fist['count'];
                            $service_price += $fist['price'];
                        }
                    }
//                    dd(
//                        $driver_id
//                    );
                }
            }
        }
        $temp_collection_service->flatten(1)
                                ->groupBy('type_key')
                                ->map(function ($group, $index) use(&$services_for_artem) {

//                $group = $group;
                                    $services_for_artem->push([
                                        'id'       => $group->first()['id'],
                                        'name'     => $group->first()['name'],
                                        'discount' => round($group->first()['discount'] ?? 0),
                                        'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ??
                                                                                                 0)
                                                                                          / 100) - 1)),
                                        'count'    => $group->count(),

                                        'type'        => $index,
                                        //                                        'type_anketa' => $group->first()['type_anketa'],
                                    ]);
                                })
                                ->values();
        $data = $result;
        $result = [];
        $result['data'] = $data;
        $result['services'] = [
            'count' => $service_counter,
            'price' => $service_price,
            'services_for_artem' => $services_for_artem
//                ->push([
//                    'count' => $total_dop_ebat,
//                    'type' => 'is_dop',
//                ])
                ->groupBy('type')
        ];
//        dd($result);
        return array_reverse($result);
    }

    public function getJournalTechsOther($company, $date_from, $date_to, $products, $discounts)
    {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa', 'pechat_pl'])
                         ->with([
                             'driver.contracts.services',
                             'company.contracts.services',
                         ])
                         ->where(function ($query) use ($company) {
                             $query->where('anketas.company_id', $company->hash_id)
                                   ->orWhere('anketas.company_name', $company->name);
                         })
                         ->where('in_cart', 0)
                         ->whereBetween('anketas.created_at', [
                             $date_from,
                             $date_to,
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
                         ->get();

        $result = [];
        $total_dop_ebat = 0;

        $companyProdsID = $company
            ->contracts->whereIn('id', $this->contracts_ids)
                       ->pluck('services')
                       ->flatten()
                       ->pluck('id')
                       ->toArray();

        foreach ($reports as $report) {
            if ($report->car->id) {
                if ($services = $report->car
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>',
                                   ($report->date
                                       ? Carbon::parse($report->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<',
                                   ($report->date
                                       ? Carbon::parse($report->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->first()) {

                    $services = $services->services;
                } else {
                    continue;
                }
            } else {
                if ($services = $report->company
                    ->contracts->whereIn('id', $this->contracts_ids)
                               ->where(
                                   'date_of_end', '>',
                                   ($report->date
                                       ? Carbon::parse($report->date)->subDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where(
                                   'date_of_start', '<',
                                   ($report->date
                                       ? Carbon::parse($report->date)->addDay()->format('Y-m-d')
                                       :
                                       Carbon::createFromFormat('Y-m', $report->period_pl)->startOfMonth())
                               )
                               ->where("main_for_company", 1)
                               ->first()) {
                    $services = $services->services;
                } else {
                    continue;
                }
            }


            $flagEbat = false;
            try {
                if ($report->period_pl) {
                    $date = Carbon::parse($report->period_pl);
                } else {
                    $date = Carbon::parse($report->date);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year.'-'.$date->month; // key by date

            $result[$key]['year']                                       = $date->year;
            $result[$key]['month']                                      = $date->month;
            $result[$key]['reports'][$report->car_id]['car_gos_number'] = $report->car->gos_number;
            $result[$key]['reports'][$report->car_id]['type_auto']      = $report->car->type_auto;
            $result[$key]['reports'][$report->car_id]['pv_id']          = implode('; ',
                array_unique($reports->where('car_id', $report->car_id)->pluck('pv_id')->toArray()));

            $total = $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total']
                = ($result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->car_id]['types']['is_dop']['total']
                    = ($result[$key]['reports'][$report->car_id]['types']['is_dop']['total'] ?? 0) + 1;
                $total_dop_ebat++;
                $flagEbat = true;
            }


            $types = explode('/', $report->type_view);
//            $prods = $services;

            if ($services->count() > 0) {
                foreach ($services as $service) {
                    if ($report->type_anketa !== $service->type_anketa) {
                        continue;
                    }

                    $disc              = $discounts->where('products_id', $service->id);
                    $service->price    = $service->pivot->service_cost;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price    = $service->pivot->service_cost - ($service->pivot->service_cost
                                                                                      * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'tech') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sync']
                                    = in_array($service->id, $companyProdsID);

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum']
                                        = $service->pivot->service_cost * $total;
                                } else {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum']
                                        = $service->pivot->service_cost;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['discount']
                                        = $service->discount;
                                }
                            }
                        }
                    }

                    if($flagEbat?? false){
                        $result[$key]['reports'][$report->car_id]['types']['is_dop']['services'][] = [
                            'sum'      => $result[$report->car_id]['types'][$report->type_view]['sum'] ?? 0,
                            'discount' => $result[$report->car_id]['types'][$report->type_view]['discount'] ?? 0,
                            'name'     => $service->name ?? '',
                            'type_key'       => $report->type_view,
                            'id'       => $service->id ?? ''
                        ];
                    }else{
                        $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['services'][] = [
                            'sum'      => $result[$report->car_id]['types'][$report->type_view]['sum'] ?? 0,
                            'discount' => $result[$report->car_id]['types'][$report->type_view]['discount'] ?? 0,
                            'type_key'       => $report->type_view,
                            'name'     => $service->name ?? '',
                            'id'       => $service->id ?? ''
                        ];
                    }
                }
            }
        }


        $service_counter = 0;
        $service_price = 0;
        $temp_collection_service = collect();
        $services_for_artem = collect();
        foreach ($result as $key => $period_info){
            foreach ($period_info['reports'] as $car_id => $fcn_info){
                foreach ($fcn_info['types'] as $type_key => $type_info){
                    if($result[$key]['reports'][$car_id]['types'][$type_key]['services'] ?? false){
                        $temp_collection_service->push($result[$key]['reports'][$car_id]['types'][$type_key]['services']);
                        $result[$key]['reports'][$car_id]['types'][$type_key]['services'] = collect($result[$key]['reports'][$car_id]['types'][$type_key]['services'])
                            ->groupBy('id')
                            ->map(function ($group) use($type_key, &$services_for_artem) {
//                                $services_for_artem->push([
//                                    'id'       => $group->first()['id'],
//                                    'name'     => $group->first()['name'],
//                                    'discount' => round($group->first()['discount'] ?? 0),
//                                    'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ?? 0)
//                                                                                      / 100) - 1)),
//                                    'count'    => $group->count(),
//
//                                    'type' => $type_key
//                                ]);
                                return [
                                    'id'       => $group->first()['id'],
                                    'name'     => $group->first()['name'],
                                    'discount' => $group->first()['discount'],
                                    'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ?? 0)
                                                                                      / 100) - 1)),
                                    'count'    => $group->count(),
                                    'type' => $type_key
                                ];
                            })
                            ->values();
                        if ($fist = $result[$key]['reports'][$car_id]['types'][$type_key]['services']->first()) {
                            $result[$key]['reports'][$car_id]['types'][$type_key]['count'] = $fist['count'];
                            $service_counter += $fist['count'];
                            $service_price += $fist['price'];
                        }
                    }
                }
            }
        }
        $temp_collection_service->flatten(1)
                                ->groupBy('type_key')
                                ->map(function ($group, $index) use( &$services_for_artem) {

//                $group = $group;
                                    $services_for_artem->push([
                                        'id'       => $group->first()['id'],
                                        'name'     => $group->first()['name'],
                                        'discount' => round($group->first()['discount'] ?? 0),
                                        'price'    => -(($group->first()['sum'] ?? 0) * ((intval($group->first()['discount'] ??
                                                                                                 0)
                                                                                          / 100) - 1)),
                                        'count'    => $group->count(),

                                        'type'        => $index,
                                        //                                        'type_anketa' => $group->first()['type_anketa'],
                                    ]);
                                })
                                ->values();
        $data = $result;
        $result = [];
        $result['data'] = $data;
        $result['services'] = [
            'count' => $service_counter,
            'price' => $service_price,
            'services_for_artem' => $services_for_artem
//                ->push([
//                'count' => $total_dop_ebat,
//                'type' => 'is_dop',
//            ])
                ->groupBy('type')
        ];
        return array_reverse($result);
    }

    public function getJournalOther($company, $services)
    {
        $result = [];

        $companyServices = $company
            ->contracts
            ->pluck('services')
            ->flatten();

        $services = $services->where('type_product', 'Абонентская плата без реестров');

        $drivers = Driver::with(['contracts.services'])
                         ->where('company_id', $company->id)
                         ->get();

        $cars = Car::with(['contracts.services'])
                   ->where('company_id', $company->id)
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
                        'name'       => $service->name,
                        'sum'        => 1 * $service->pivot->service_cost,
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
                        'type_auto'  => $car->type_auto,
                        'name'       => $service->name,
                        'sum'        => 1 * $service->pivot->service_cost,
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

}
