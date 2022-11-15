<?php

namespace App\Http\Controllers\Reports;

use App\Anketa;
use App\Company;
use App\Discount;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyServicesRefactoring extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('reports.company-service-refactoring');
    }

    /*
     * На выход:
     * [
     *      contract_id => 1,
     *      inspection_type => medick,
     *      inspections => [
     *          cars => [
     *              car_id = 1,
     *              ...
     *              type_view => [
     *              ]
     *          ],
     * ]
     */
    public function getReport(Request $request)
    {
        $discounts = Discount::get();

        $company      = Company::whereHashId($request->get('company_id'))->first();
        $contracts_id = $request->get('contracts_id');

        $date_from = $request->get('month')
            ? Carbon::parse($request->get('month'))->startOfMonth()
            : Carbon::now()->startOfYear()->startOfMonth();

        $date_to = $date_from->clone()->endOfMonth();


        return response([
            'status' => true,
            'result' => [
                'medic'
            ]
        ]);
    }

    public function export(Request $request)
    {
        $company      = Company::whereHashId($request->get('company_id'))->first();
        $contracts_id = $request->get('contracts_id');

        $date_from = $request->get('month')
            ? Carbon::parse($request->get('month'))->startOfMonth()
            : Carbon::now()->startOfYear()->startOfMonth();

        $date_to = $date_from->clone()->endOfMonth();


        return [
            'medics' => $this->getJournalMedic($company, $contracts_id, $date_from, $date_to, null),
            //            'techs'        => $this->getJournalTechs($company, $date_from, $date_to, $services, $discounts),
            //            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to, $services, $discounts),
            //            'techs_other'  => $this->getJournalTechsOther($company, $date_from, $date_to, $services, $discounts),
            //            'other'        => $this->getJournalOther($company, $services),
        ];
    }

    public function getJournalMedic($company, $contracts_id, $date_from, $date_to, $discounts)
    {
        $result = collect();

        $medics = Anketa::whereIn('type_anketa', [
            'medic',
            'bdd',
            'report_cart',
            'pechat_pl',
        ])
                        ->whereIn('anketas.contract_id', $contracts_id)
                        ->with([// 'services_snapshot',
                                'driver',
                                'contract.services',
                        ])
                        ->where(function ($query) use ($company) {
                            $query->where('company_id', $company->hash_id)
                                  ->orWhere('company_name', $company->name);
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


        $drivers = $medics
            ->pluck('driver')
            ->keyBy('id')
            ->values();

        $types_view = $medics
            ->pluck('type_view')
            ->unique();

        $servicesForMedics = $medics
            ->pluck('contract')
            ->pluck('services')
            ->flatten()
            ->keyBy('id')
            ->values();


        foreach ($drivers as $driver) {
            $dataForDriver    = collect();
            $dataForDriverArr = [];

            $dataForDriverArr = [
                'driver'     => $driver,
                'pv_address' => $medics
                    ->where('car_id', $driver->hash_id)
                    ->pluck('pv_id')
                    ->unique()
                    ->implode('; '),
                'type_views' => [
                    [
                        'type_view' => 'pedreisovii',
                        'count'     => 1,
                        'sum'       => 700,
                        'discount'  => 50,
                    ],
                    [
                        'type_view' => 'total_fo_driver',
                        'count'     => 2,
                        'sum'       => 1400,
                    ],
                    //                    ...
                ],
            ];


        }

        foreach ($drivers as $driver) {
//            $driver_id  = $driver->hash_id;
//            $driver_fio = $driver->fio;

            $type_views = [];


            foreach ($medics
                         ->where('type_anketa', 'medic')
                         ->pluck('type_view')
                         ->unique() as $type_view
            ) {

                $total_for_type_view = $medics->where('type_view', $type_view)
                                              ->count();

                $type_explode = explode('/', $type_view);

                foreach ($servicesForMedics as $service) {
                    $service->price = $service->pivot->service_cost;


                    if ($discountsForTech = $discounts->where('products_id', $service->id)) {
                        foreach ($discountsForTech as $discount) {
                            $disSum = $discount->getDiscount($total_for_type_view);
                            if ($disSum) {
                                $price_for_service    = $service->pivot->service_cost
                                                        - ($service->pivot->service_cost
                                                           * $disSum / 100);
                                $discount_for_service = $disSum;
                            }
                        }
                    }

                    $vt = $service->type_view;

                    foreach ($type_explode as $mini_type) {
                        if (strpos($vt, $mini_type) !== false) {
                            if ($service->type_product === 'Разовые осмотры') {
                                $sum_for_type_view = ($price_for_service ?? false) ?
                                    ($price_for_service * $total_for_type_view)
                                    : $service->price;
                            } else {
                                $sum_for_type_view = ($price_for_service ?? false)
                                    ? ($price_for_service * $total_for_type_view)
                                    : $service->price;

                            }
                        }
                    }

                    $type_views[] = [
                        'count'     => $total_for_type_view,
                        'discount'  => $discount_for_service ?? 0,
                        'sum'       => $sum_for_type_view ?? 0,
                        'type_view' => $type_view,
                    ];
                }
            }
            foreach ($medics
                         ->pluck('type_view')
                         ->unique() as $type_view
            ) {
                $total_for_type_view = $medics->where('type_view', $type_view)
                                              ->count();

                $result[$driver->hash_id]['types'][$type_view]['total'] = $total_for_type_view;

                foreach ($servicesForMedics as $service) {
                    $service->price = $service->pivot->service_cost;


                    if ($discountsForTech = $discounts->where('products_id', $service->id)) {
                        foreach ($discountsForTech as $discount) {
                            $disSum = $discount->getDiscount($total_for_type_view);
                            if ($disSum) {
                                $price_for_service    = $service->pivot->service_cost
                                                        - ($service->pivot->service_cost
                                                           * $disSum / 100);
                                $discount_for_service = $disSum;
                            }
                        }
                    }

                    if ($service->type_product === 'Разовые осмотры') {
                        $sum_for_type_view = $service->price * $total_for_type_view;
                    } else {
                        $sum_for_type_view = $service->price;
                    }

                    $type_views[] = [
                        'count'     => $total_for_type_view,
                        'discount'  => $discount_for_service ?? 0,
                        'sum'       => $sum_for_type_view ?? 0,
                        'type_view' => $type_view,
                    ];
                }
            }

            $result[]   = [
                'driver'     => $driver,
                'pv_address' => $medics
                    ->where('car_id', $driver->hash_id)
                    ->pluck('pv_id')
                    ->unique()
                    ->implode('; '),
                'type_views' => $type_views
            ];
        }



        return $result;
    }

    // поиск услуг для компании Отчет по услугам компании
    public function getContractsForCompany(Request $request)
    {
        $company = Anketa::with('contract')
                         ->where('company_id', $request->id)
                         ->whereNotNull('contract_id')
                         ->whereHas('contract')
                         ->groupBy('contract_id')
                         ->get()
                         ->pluck('contract')
                         ->map(function ($q) {
                             return [
                                 'name' => $q->name,
                                 'id'   => $q->id,
                             ];
                         });

        return response($company);
    }


}
