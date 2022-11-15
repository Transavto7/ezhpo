<?php

namespace App\Http\Controllers\Reports;

use App\Anketa;
use App\Company;
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

    }

    public function export(Request $request)
    {
        $company = Company::whereHashId($request->get('company_id'))->first();
        $contracts_id = $request->get('contracts_id');

        $date_from = $request->get('month')
            ? Carbon::parse($request->get('month'))->startOfMonth()
            : Carbon::now()->startOfYear()->startOfMonth();

        $date_to        = $date_from->clone()->endOfMonth();



        return [
            'medics'       => $this->getJournalMedic($company, $contracts_id, $date_from, $date_to),
//            'techs'        => $this->getJournalTechs($company, $date_from, $date_to, $services, $discounts),
//            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to, $services, $discounts),
//            'techs_other'  => $this->getJournalTechsOther($company, $date_from, $date_to, $services, $discounts),
//            'other'        => $this->getJournalOther($company, $services),
        ];
    }
    public function getJournalMedic($company, $contracts_id, $date_from, $date_to)
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
            $dataForDriver = collect();
            $dataForDriverArr = [];

            $dataForDriverArr = [
                'driver' => $driver,
                'pv_address' => $medics
                    ->where('car_id', $driver->hash_id)
                    ->pluck('pv_id')
                    ->unique()
                    ->implode('; '),
                'type_views' => [
                    [
                        'type_view' => 'pedreisovii',
                        'count' => 1,
                        'sum' => 700,
                        'discount' => 50
                    ],[
                    [
                        'type_view' => 'total_fo_driver',
                        'count' => 2,
                        'sum' => 1400
                    ],
//                    ...
                ]
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
