<?php

namespace App\Http\Controllers\Reports;

use App\Anketa;
use App\Car;
use App\Company;
use App\Discount;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportControllerContract;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyServicesRefactoring extends Controller
{
    private $inspectionsGroups;
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
//        $company      = Company::whereHashId($request->get('company_id'))->first();
        $contracts_id = $request->get('contracts_id');
        $company_id   = $request->get('company_id');
        $company      = Company::whereHashId($company_id)->first();


        $report = new ReportControllerContract();

        dd(
            $this - $this->techHandle()
        );

//        dd(
//            Car::with(['inspections_tech'])
//               ->where('hash_id', 848701)
//               ->first()
//               ->toArray()
//        );


//        $report->getReport();


        $this->changeFilters((object)$request->all())
             ->handle();


//        foreach ($inspectionsGroups as $groupKey => $inspectionsGroup) {
//            switch ($groupKey) {
//                case 'tech':
//
//            }
//        }

        return response([
            'status' => true,
            'result' => [
                'medic',
            ],
        ]);
    }

    public function handle()
    {
//         collect();

        foreach ($this->inspectionsGroups as $group_key => $inspectionsGroup) {
            switch ($group_key) {
                case 'tech':
//                    $result->push(
//                        $this->techHandle($inspectionsGroup)
//                    );
            }
        }
    }


    private function techHandle($inspections)
    {
        $date_from = $this->date_from;
        $date_to   = $this->date_to;



        $contracts = Contract::with([
                'cars.inspections_tech',
                'drivers.inspections_medic',
                'drivers.inspections_pechat_pl',
                'drivers.inspections_bdd',
                'drivers.inspections_report_cart',
                'cars.contracts.services',
                'drivers.contracts.services',
                'company.contracts.services',
        ])
            ->whereIn('id', $this->contracts->pluck('id'))
            ->whereHas('company', function ($q){
                $q->where('company.id', $this->company->id);
            })
            ->whereHas('cars.inspections_tech', function ($q) use ($date_from, $date_to) {
                $q->where('anketas.in_cart', 0)
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
            })
                             ->get()
        ->map(function ($contract){

        });


        dd(
            $contracts->toArray()
        );


















        $cars = Car::with([
            'contracts.services.discount',
            'inspections_tech.company.services.discount',
        ])
                   ->whereHas('inspections_tech', function ($q) use ($date_from, $date_to) {
                       $q->where('anketas.in_cart', 0)
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
                   })
                   ->where('company_id', $this->company->id)
                   ->get()
        ->map(function($car){
            $car->pv_string = $car->inspections_tech->pluck('pv_id')
                                                    ->unique()
                                                    ->implode('; ');


            $temp_var = $car->inspections_tech->groupBy('type_view');



            $car->inspections_tech = $temp_var;

            foreach ($car->inspections_tech as $type_view => &$type_view_group_inspections){

                $type_view_group_inspections = $type_view_group_inspections->map(function($inspection){
                    $inspection->contracts = $this->_getContractForCar($inspection);
                    $inspection->services = $inspection->contracts->pluck('services')
                                                                  ->flatten()
                                                                  ->where('type_view', $inspection->type_anketa);
                    return $inspection;
                });




                $total_for_type_view = $type_view_group_inspections->count();





                $car->inspections[] = [
                    'type_view' => $type_view,
                    'count' => $total_for_type_view,
                    'discount' => $services->discount->getDiscount($total_for_type_view),
                ];


//                $car->inspections[$inspection->type_anketa][$type_view]['discount'] =
                    ;


                foreach ($type_view_group_inspections as &$inspection){

                    $services = $this->_getContractForCar($inspection)
                                     ->pluck('services')
                                     ->flatten()
                                     ->where('type_view', $inspection->type_anketa);

                }

            }

        });



        foreach ($cars as $car) {



            foreach ($car->inspections_tech as $tech) {

                $services = $this->_getContractForCar($tech);
            }
        }


        return 1;
    }


    // Принимает инстанс осмотра, возвращает договор по осмотру
    private function getContractForDriver($tech)
    {

        if ($contracts = $tech->driver->contracts->whereIn('id', $this->contracts->pluck('id'))
                                                 ->where(
                                                     'date_of_end', '>',
                                                     ($tech->date
                                                         ? Carbon::parse($tech->date)->subDay()->format('Y-m-d')
                                                         :
                                                         Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                               ->startOfMonth())
                                                 )
                                                 ->where(
                                                     'date_of_start', '<',
                                                     ($tech->date
                                                         ? Carbon::parse($tech->date)->addDay()->format('Y-m-d')
                                                         :
                                                         Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                               ->startOfMonth())
                                                 )) {
        } else {
            if (
                $contracts = $tech->company->contracts->whereIn('id', $this->contracts->pluck('id'))
                                                      ->where("main_for_company", 1)
                                                      ->where(
                                                          'date_of_end', '>',
                                                          ($tech->date
                                                              ? Carbon::parse($tech->date)->subDay()
                                                                      ->format('Y-m-d')
                                                              :
                                                              Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                                    ->startOfMonth())
                                                      )
                                                      ->where(
                                                          'date_of_start', '<',
                                                          ($tech->date
                                                              ? Carbon::parse($tech->date)->addDay()
                                                                      ->format('Y-m-d')
                                                              :
                                                              Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                                    ->startOfMonth())
                                                      )
            ) {

            }
        }

        return $contracts;
    }

    private function _getContractForCar($tech)
    {

        if ($contracts = $tech->car->contracts->whereIn('id', $this->contracts->pluck('id'))
                                              ->where(
                                                  'date_of_end', '>',
                                                  ($tech->date
                                                      ? Carbon::parse($tech->date)->subDay()->format('Y-m-d')
                                                      :
                                                      Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                            ->startOfMonth())
                                              )
                                              ->where(
                                                  'date_of_start', '<',
                                                  ($tech->date
                                                      ? Carbon::parse($tech->date)->addDay()->format('Y-m-d')
                                                      :
                                                      Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                            ->startOfMonth())
                                              )) {
        } else {
            if (
                $contracts = $tech->company->contracts->whereIn('id', $this->contracts->pluck('id'))
                                                      ->where("main_for_company", 1)
                                                      ->where(
                                                          'date_of_end', '>',
                                                          ($tech->date
                                                              ? Carbon::parse($tech->date)->subDay()
                                                                      ->format('Y-m-d')
                                                              :
                                                              Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                                    ->startOfMonth())
                                                      )
                                                      ->where(
                                                          'date_of_start', '<',
                                                          ($tech->date
                                                              ? Carbon::parse($tech->date)->addDay()
                                                                      ->format('Y-m-d')
                                                              :
                                                              Carbon::createFromFormat('Y-m', $tech->period_pl)
                                                                    ->startOfMonth())
                                                      )
            ) {

            }
        }

        return $contracts;
    }


    public function changeFilters($data)
    {
        $this->date_from = $data->month
            ? Carbon::parse($data->month)->startOfMonth()
            : Carbon::now()->startOfYear()->startOfMonth();

        $this->date_to = $this->date_from->clone()->endOfMonth();
        $this->company = Company::whereHashId($data->company_id)->first();

        $this->contracts = Contract::where('id', $data->contracts_id)
                                   ->get(['id', 'name']);

        return $this->_prepare_inspections_groups();
    }


    // huita
    private function _prepare_inspections_groups()
    {
        $date_from    = $this->date_from;
        $date_to      = $this->date_to;
        $company      = $this->company;
        $contracts_id = $this->contracts_id;

        $this->inspectionsGroups
            = Anketa::with([
            'driver.contracts.services',
            'car.contracts.services',
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
                    ->get()
                    ->groupBy('type_anketa');

        return $this;
    }

    private function _inspectionsHandler()
    {

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
