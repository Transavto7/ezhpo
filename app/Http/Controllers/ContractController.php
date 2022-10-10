<?php

namespace App\Http\Controllers;

use App\Car;
use App\Company;
use App\Driver;
use App\FieldPrompt;
use App\Models\Contract;
use App\Models\Service;
use App\Product;
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

        if ($filters['sortBy'] == 'company.name') {
            $contracts->join('companies', 'company_id', 'id')
                      ->orderBy('companies.name', $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC')
                      ->select('contracts.*');
        } elseif ($filters['sortBy'] == 'company.inn') {
            $contracts->join('companies', 'company_id', 'id')
                      ->orderBy('companies.inn', $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC')
                      ->select('contracts.*');
        } else {
            $contracts->orderBy($filters['sortBy'], $filters['sortDesc'] == 'true' ? 'DESC' : 'ASC');
        }


        $contracts = $contracts->paginate($filters['perPage'], $columns = ['*'], $pageName = 'page',
            $page = $filters['currentPage']);

        return response([
            'status' => true,
            'result' => [
                'contracts' => $contracts->getCollection(),
                'total'     => $contracts->total(),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $data_to_save = json_decode($request->get('data_to_save'), true);

        $services = $data_to_save['services'] ?? [];
        unset($data_to_save['services']);

        $contract = Contract::create([
            'name'           => $data_to_save['name'] ?? null,
            'date_of_end'    => $data_to_save['date_of_end'] ?? null,
            'sum'            => $data_to_save['sum'] ?? null,
            'company_id'     => $data_to_save['company']['id'] ?? null,
            'our_company_id' => $data_to_save['our_company']['id'] ?? null,
        ]);

//        $collectServices = collect($services);
        $servicesToSync = [];
        foreach ($services as $service) {
            $servicesToSync[$service['id']] = ['service_cost' => $service['price_unit']];
        }


        $contract->services()->sync($servicesToSync);

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
            $servicesToSync[$service['id']] = ['service_cost' => $service['pivot']['service_cost'] ?? $service['price_unit']];
        }

        $contract->services()->sync($servicesToSync);

        $contract->update([
            'name'           => $data_to_save['name'] ?? null,
            'date_of_end'    => $data_to_save['date_of_end'] ?? null,
            'sum'            => $data_to_save['sum'] ?? null,
            'company_id'     => $data_to_save['company']['id'] ?? null,
            'our_company_id' => $data_to_save['our_company']['id'] ?? null,
        ]);

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
        return response([
            'status'    => true,
            'contracts' => Contract::where('company_id', $request->company_id)->get(),
        ]);
    }
}
