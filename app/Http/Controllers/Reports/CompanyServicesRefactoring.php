<?php

namespace App\Http\Controllers\Reports;

use App\Company;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyServicesRefactoring extends Controller
{
    private $date_to;
    private $date_from;
    private $company;
    private $contracts;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('reports.company-service-refactoring');
    }

    public function getReport(Request $request)
    {
        $this->contracts = Contract::whereIn('id', $request->get('contracts_id', []))
                                   ->get();
        $this->company   = Company::whereHashId($request->get('company_id'))
                                  ->first();

        $this->date_from = $request->month
            ? Carbon::parse($request->month)->startOfMonth()
            : Carbon::now()->startOfYear()->startOfMonth();

        $this->date_to = $this->date_from->clone()->endOfMonth();

//        dd(
//            json_decode($this->handle(), true)
//        );
        return response([
            'status' => true,
            'result' => $this->handle(),
        ]);
    }


    public function handle()
    {
        $date_from = $this->date_from;
        $date_to   = $this->date_to;

        $types_inspections = [
            'tech'        => 'Техосмотры',
            'medic'       => 'Медосмотры',
            'pechat_pl'   => 'Печать путевых листов',
            'bdd'         => 'БДД',
            'report_cart' => 'Отчёты с карт',
        ];

        return Contract::with([
            'cars.inspections_tech'           => function ($inspection) use ($date_from, $date_to) {
                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
            },
            'drivers.inspections_medic'       => function ($inspection) use ($date_from, $date_to) {
                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
            },
            'drivers.inspections_pechat_pl'   => function ($inspection) use ($date_from, $date_to) {
                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
            },
            'drivers.inspections_bdd'         => function ($inspection) use ($date_from, $date_to) {
                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
            },
            'drivers.inspections_report_cart' => function ($inspection) use ($date_from, $date_to) {
                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
            },
            'cars.contracts.services',
            'drivers.contracts.services',
            'company.contracts.services',

//            'company.inspections_tech'        => function ($inspection) use ($date_from, $date_to) {
//                $inspection->whereDoesntHave('car');
//                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
//            },
//            'company.inspections_medic'       => function ($inspection) use ($date_from, $date_to) {
//                $inspection->whereDoesntHave('driver');
//                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
//            },
//            'company.inspections_pechat_pl'   => function ($inspection) use ($date_from, $date_to) {
//                $inspection->whereDoesntHave('driver');
//                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
//            },
//            'company.inspections_bdd'         => function ($inspection) use ($date_from, $date_to) {
//                $inspection->whereDoesntHave('driver');
//                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
//            },
//            'company.inspections_report_cart' => function ($inspection) use ($date_from, $date_to) {
//                $inspection->whereDoesntHave('driver');
//                $this->_getQueryToFilterDatesAnketass($inspection, $date_from, $date_to);
//            },


            'services.discount',
        ])
                       ->whereIn('id', $this->contracts->pluck('id'))
                       ->whereHas('company', function ($q) {
                           $q->where('id', $this->company->id);
                       })
                       ->get()
                       ->map(function ($contract) use ($types_inspections) {
                           $contract->inspections = collect();
                           foreach ($types_inspections as $inspection_type => $name_type) {

                               if ($inspection_type === 'tech') {
                                   $this->_prepare_inspections_car($contract, $name_type);
                               } else {
                                   $this->_prepare_inspections_driver($contract, $inspection_type, $name_type);
//                                   dd(
//                                       $contract->toArray()
//                                   );
                               }

                           }


                           return $contract;
                       });
    }

    private function _prepare_inspections_car(&$contract, $name_type)
    {
        $contract->inspections->push(collect([
            'name' => $name_type,
            'data' => $contract->cars
                ->map(function ($car) use ($contract) {
                    $car->types
                        = $this->_handleTypes($car['inspections_tech'], $contract);

                    return $car;
                }),
        ]));
    }

    private function _prepare_inspections_driver(&$contract, $type_inspection, $name_type)
    {
        $contract->inspections->push(collect([
            'name' => $name_type,
            'data' => $contract->drivers
                ->map(function ($driver) use ($contract, $type_inspection) {
                    $driver->types
                        = $this->_handleTypes($driver['inspections_'.$type_inspection], $contract);

                    return $driver;
                })->push($this->_handleTypes($contract->company['inspections_'.$type_inspection], $contract)),
        ]));
    }

    private function _handleTypes($inspections, $contract)
    {
        return $inspections->filter(function ($inspection) {
            return ( !$inspection->is_dop || ($inspection->result_dop != null));
        })
                           ->groupBy('type_view')
                           ->map(function ($group, $type_view) use ($contract) {
                               $services = collect();

                               if ($group->isNotEmpty()) {
                                   $services = $contract->services
                                       ->where('type_anketa', $group[0]->type_anketa)
                                       ->where('type_view');
                               }

                               $explode_type_view_inspection = explode('/', $type_view);

                               $group->services = $services->map(function ($service) use (
                                   $group,
                                   $explode_type_view_inspection
                               ) {
                                   // Проверка на тип осмотра - костыль
                                   $flag_is_our_type_view = false;

                                   foreach ($explode_type_view_inspection as $type) {
                                       if (strpos($service->type_view, $type) !== false) {
                                           $flag_is_our_type_view = true;
                                       }
                                   }
                                   if ( !$flag_is_our_type_view) {
                                       return null;
                                   }

//                                   dd(
//                                       $service->pivot->service_cost, $group->toArray()
//                                   );
                                   if ($service->type_product === 'Разовые осмотры') {
                                       return collect([
                                           'name'     => $service->name,
                                           'price'    => $service->pivot->service_cost
                                                         * $group->count(),
                                           'discount' => $service->discount->getDiscount($group->count()),
                                       ]);
                                   } else {
                                       return collect([
                                           'name'     => $service->name,
                                           'price'    => $service->pivot->service_cost,
                                           'discount' => $service->discount->getDiscount($group->count()),
                                       ]);
                                   }
                               })->filter(function ($q) {
                                   return $q;
                               });

                               return collect([
                                   'name'     => $type_view,
                                   'services' => $group->services->values(),
                                   'count'    => $group->count(),
                               ]);
                           })
                           ->values();
    }

    private function _getQueryToFilterDatesAnketass($query, $date_from, $date_to)
    {

        return $query->where('anketas.in_cart', 0)
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
                     });
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
}
