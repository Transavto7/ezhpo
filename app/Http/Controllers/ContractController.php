<?php

namespace App\Http\Controllers;

use App\Car;
use App\Company;
use App\Driver;
use App\FieldPrompt;
use App\Models\Contract;
use App\Models\Service;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        return view('contract.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $contracts = Contract::with(['company', 'our_company', 'services']);
        $filters   = $request->all();
//        dd(
//            $filters
//        );
        $filters['sortBy'] = $filters['sortBy'] ?? 'id';
        $filters['sortDesc'] = $filters['sortDesc'] ?? 'true';
        $filters['perPage'] = $filters['perPage'] ?? 15;
        $filters['currentPage'] = $filters['currentPage'] ?? 1;

        if ($filters['sortBy'] == 'company.name') {
            $contracts->leftJoin('companies', 'company_id', 'companies.id')
                      ->orderBy('companies.name', $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC')
                      ->select('contracts.*');
        } elseif ($filters['sortBy'] == 'our_company.name') {
            $contracts->leftJoin('reqs', 'our_company_id', 'reqs.id')
                      ->orderBy('reqs.name', $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC')
                      ->select('contracts.*');
        } else {
            $contracts->orderBy($filters['sortBy'], $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC');
        }

        if ($filters['trash'] ?? false) {
            $contracts->onlyTrashed();
        }
        if ($filters['id'] ?? false) {
            $contracts->where('id', $filters['id']);
        }
        if ($filters['name'] ?? false) {
            $contracts->where('name', 'like', "%{$filters['name']}%");
        }
        if ($filters['service_id'] ?? false) {
            $contracts->whereHas('services', function ($q) use ($filters) {
                $q->where('services.id', $filters['service_id']);
            });
        }
        if ($filters['company_id'] ?? false) {
            $contracts->whereHas('company', function ($q) use ($filters) {
                $q->where('companies.id', $filters['company_id']);
            });
        }
        if ($filters['our_company_id'] ?? false) {
            $contracts->whereHas('our_company', function ($q) use ($filters) {
                $q->where('reqs.id', $filters['our_company_id']);
            });
        }

        if ($filters['date_of_end_start'] ?? false) {
            $contracts->whereDate('date_of_end', '>=', $filters['date_of_end_start']);
        }
        if ($filters['date_of_end_end'] ?? false) {
            $contracts->whereDate('date_of_end', '<=', $filters['date_of_end_end']);
        }

//        if (isset($filters['main_for_company'])) {
//            if ($filters['main_for_company'] == 0 || $filters['main_for_company'] == 1) {
//                $contracts->where('main_for_company', $filters['main_for_company']);
//            }
//        }

        $contracts = $contracts->paginate(
            $request->all()['nikita_yeban'] ?? $request->all()['or_on_soset_chlen'] ?? 500,
            $columns = ['*'],
            $pageName = 'page',
            $page = $filters['currentPage']
        );

        return response([
            'status' => true,
            'result' => [
                'contracts'   => $contracts->getCollection(),
                'total'       => $contracts->total(),
                'currentPage' => $contracts->currentPage(),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $data_to_save = json_decode($request->get('data_to_save'), true);
//dd($data_to_save);
        $services = $data_to_save['services'] ?? [];
        unset($data_to_save['services']);

        $contract = Contract::create([
            'name'           => $data_to_save['name'] ?? null,
            'date_of_end'    => isset($data_to_save['date_of_end']) ? Carbon::parse($data_to_save['date_of_end'])
                : null,
            'sum'            => $data_to_save['sum'] ?? null,
            'company_id'     => $data_to_save['company']['id'] ?? null,
            'our_company_id' => $data_to_save['our_company']['id'] ?? null,
            //            'main_for_company' => $data_to_save['main_for_company'] ?? 0,
        ]);


        $servicesToSync = [];
        foreach ($services as $service) {
            $servicesToSync[$service['id']] = ['service_cost' => $service['price_unit']];
        }


        $contract->services()->sync($servicesToSync);

        if ($data_to_save['company']['id'] ?? false) {
            $services = $contract->services;

            $is_cars_services    = false;
            $is_drivers_services = false;

            foreach ($services as $service) {
                if ($service->essence == \App\Service::ESSENCE_DRIVER) {
                    $is_drivers_services = true;
                }
                if ($service->essence == \App\Service::ESSENCE_CAR) {
                    $is_cars_services = true;
                }
                if ($service->essence == \App\Service::ESSENCE_CAR_DRIVER) {
                    $is_cars_services    = true;
                    $is_drivers_services = true;
                }

            }

            if($is_cars_services){
                $cars_update = Car::where('company_id', $data_to_save['company']['id']);

                // Если жёстко, то не проверяем старые записи в компаниях
                if ( !($data_to_save['hard_reset_for_car_and_drivers'] ?? false)) {
                    $cars_update->whereDoesntHave('contract');
                }

                $cars_update->update([
                    'contract_id' => $contract->id,
                ]);
            }

            if($is_drivers_services){
                $drivers_update = Driver::where('company_id', $data_to_save['company']['id']);

                if (!($data_to_save['hard_reset_for_car_and_drivers'] ?? false)) {
                    $drivers_update->whereDoesntHave('contract');
                }
                $drivers_update->update([
                    'contract_id' => $contract->id,
                ]);
            }
        }

        return response([
            'status'   => true,
            'contract' => $contract,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    public function update(Request $request)
    {
        $data_to_save = $request->post('data_to_save');

        if ( !$contract = Contract::find($data_to_save['id'])) {
            return response([
                'status'  => false,
                'message' => 'Не найдено',
            ]);
        }

        $services = $data_to_save['services'] ?? [];
        unset($data_to_save['services']);

        $servicesToSync = [];
        foreach ($services as $service) {
            $servicesToSync[$service['id']] = [
                'service_cost' => $service['pivot']['service_cost'] ?? $service['price_unit'],
            ];
        }
        $contract->update([
            'name'           => $data_to_save['name'] ?? null,
            'date_of_end'    => isset($data_to_save['date_of_end']) ? Carbon::parse($data_to_save['date_of_end'])
                : null,
            'sum'            => $data_to_save['sum'] ?? null,
            'company_id'     => $data_to_save['company']['id'] ?? null,
            'our_company_id' => $data_to_save['our_company']['id'] ?? null,
            //            'main_for_company' => $data_to_save['main_for_company'] ?? 0,
        ]);

        $contract->services()->sync($servicesToSync);

        if ($data_to_save['company']['id'] ?? false) {
            $services = $contract->services;

            $is_cars_services    = false;
            $is_drivers_services = false;

            foreach ($services as $service) {
                if ($service->essence == \App\Service::ESSENCE_DRIVER) {
                    $is_drivers_services = true;
                }
                if ($service->essence == \App\Service::ESSENCE_CAR) {
                    $is_cars_services = true;
                }
                if ($service->essence == \App\Service::ESSENCE_CAR_DRIVER) {
                    $is_cars_services    = true;
                    $is_drivers_services = true;
                }

            }

            if($is_cars_services){
                $cars_update = Car::where('company_id', $data_to_save['company']['id']);

                // Если жёстко, то не проверяем старые записи в компаниях
                if ( !($data_to_save['hard_reset_for_car_and_drivers'] ?? false)) {
                    $cars_update->whereDoesntHave('contract');
                }

                $cars_update->update([
                    'contract_id' => $contract->id,
                ]);
            }

            if($is_drivers_services){
                $drivers_update = Driver::where('company_id', $data_to_save['company']['id']);

                if ( !($data_to_save['hard_reset_for_car_and_drivers'] ?? false)) {
                    $drivers_update->whereDoesntHave('contract');
                }
                $drivers_update->update([
                    'contract_id' => $contract->id,
                ]);
            }
        }

        return response([
            'status'   => true,
            'contract' => $contract,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        return response([
            'status'   => true,
            'contract' => Contract::find($id)->delete(),
        ]);
    }

    public function restore($id, Request $request)
    {
        return response([
            'status'   => true,
            'contract' => Contract::withTrashed()->find($id)->restore(),
        ]);
    }

    public function getTypes()
    {
        $types = [];
        foreach (Contract::$types as $key => $type) {
            $types[] = [
                'id'    => $key,
                'label' => $type,
            ];
        }

        return response($types);
    }

    public function getAvailableForCompany(Request $request)
    {
//        dd(Contract::where('company_id', $request->company_id)->get()->toArray());
        return response([
            'status'    => true,
            'contracts' => Contract::where('company_id', $request->company_id)->get(),
        ]);
    }
}
