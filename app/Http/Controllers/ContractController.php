<?php

namespace App\Http\Controllers;

use App\Actions\Contract\CreateContractHandler;
use App\Actions\Contract\UpdateContractHandler;
use App\Car;
use App\Driver;
use App\FieldPrompt;
use App\Models\Contract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContractController extends Controller
{
    public function view()
    {
        $permissions = [
            'create' => user()->access('contract_create'),
            'trash' => user()->access('contract_trash'),
            'read' => user()->access('contract_read'),
            'delete' => user()->access('contract_delete'),
            'edit' => user()->access('contract_edit'),
            'logs_read' => user()->access('contract_logs_read'),
        ];

        $fields = FieldPrompt::where('type', 'contracts')->get();

        return view('contract.index', [
            'permissions' => $permissions,
            'fields' => $fields
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $contracts = Contract::with(['company', 'our_company', 'services', 'drivers', 'cars']);
        $filters = $request->all();
        $filters['sortBy'] = $filters['sortBy'] ?? 'id';
        $filters['sortDesc'] = $filters['sortDesc'] ?? 'true';
        $filters['perPage'] = $filters['perPage'] ?? 15;
        $filters['currentPage'] = $filters['currentPage'] ?? 1;

        $filterDirection = ($filters['sortDesc'] == 'true' || $filters['sortDesc'] == 1) ? 'DESC' : 'ASC';
        if ($filters['sortBy'] === 'company') {
            $contracts
                ->leftJoin('companies', 'company_id', 'companies.id')
                ->orderBy('companies.name', $filterDirection)
                ->select('contracts.*');
        } else if ($filters['sortBy'] === 'our_company.name') {
            $contracts
                ->leftJoin('reqs', 'our_company_id', 'reqs.id')
                ->orderBy('reqs.name', $filterDirection)
                ->select('contracts.*');
        } else if ($filters['sortBy'] === 'services') {
            $contracts
                ->withCount('services')
                ->orderBy('services_count', $filterDirection);
        } else {
            $contracts
                ->orderBy($filters['sortBy'],$filterDirection);
        }

        if ($filters['trash'] ?? false) {
            $contracts->onlyTrashed();
        }

        if ($filters['date_check_main'] ?? false) {
            $sub_ids = Contract::groupBy('company_id')
                ->select([
                    'company_id',
                    DB::raw('SUM(main_for_company) AS count')
                ])
                ->forDate($filters['date_check_main'])
                ->get()
                ->filter(function ($q) {
                    return !$q->count;
                })
                ->pluck('company_id');

            $contracts->whereIn('company_id', $sub_ids);
        }

        if ($filters['id'] ?? false) {
            $contracts->where('id', $filters['id']);
        }

        if ($filters['main_for_company'] ?? false) {
            $contracts->where('main_for_company', $filters['main_for_company']);
        }

        if ($filters['finished'] ?? false) {
            $contracts->where('finished', $filters['finished']);
        }

        if ($filters['name'] ?? false) {
            $contracts->where('name', 'like', "%{$filters['name']}%");
        }

        if ($filters['service_id'] ?? false) {
            $contracts->whereHas('services', function ($q) use ($filters) {
                $q->where('contract_service.service_id', $filters['service_id']);
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

        if ($filters['created_at_start'] ?? false) {
            $contracts->whereDate('created_at', '>=', $filters['created_at_start']);
        }

        if ($filters['created_at_end'] ?? false) {
            $contracts->whereDate('created_at', '<=', $filters['created_at_end']);
        }

        if ($filters['date_of_start'] ?? false) {
            $contracts->whereDate('date_of_start', '>=', $filters['date_of_start']);
        }

        if ($filters['date_of_end'] ?? false) {
            $contracts->whereDate('date_of_end', '<=', $filters['date_of_end']);
        }

        if (isset($filters['main_for_company'])) {
            if ($filters['main_for_company'] == 0 || $filters['main_for_company'] == 1) {
                $contracts->where('main_for_company', $filters['main_for_company']);
            }
        }

        $contracts = $contracts->paginate(
            500,
            ['*'],
            'page',
            $filters['currentPage']
        );

        return response([
            'status' => true,
            'result' => [
                'contracts' => $contracts->getCollection(),
                'total' => $contracts->total(),
                'currentPage' => $contracts->currentPage(),
            ],
        ]);
    }

    public function create(Request $request, CreateContractHandler $handler)
    {
        try {
            DB::beginTransaction();

            $data = json_decode($request->get('data_to_save'), true);

            $contract = $handler->handle($data);

            DB::commit();

            return response([
                'status' => true,
                'contract' => $contract,
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response([
                'status' => true,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function update(Request $request, UpdateContractHandler $handler)
    {
        try {
            DB::beginTransaction();

            $data = $request->input('data_to_save');

            $contract = $handler->handle($data);

            DB::commit();

            return response([
                'status' => true,
                'contract' => $contract,
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response([
                'status' => true,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $contract = Contract::find($id);

            if (empty($contract)) {
                throw new Exception('Контракт с таким ID не найден');
            }

            $contract = $contract->delete();

            DB::commit();

            return response([
                'status' => true,
                'contract' => $contract,
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response([
                'status' => true,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $contract = Contract::withTrashed()->find($id);

            if (empty($contract)) {
                throw new Exception('Контракт с таким ID не найден');
            }

            $contract = $contract->restore();

            DB::commit();

            return response([
                'status' => true,
                'contract' => $contract,
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response([
                'status' => true,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function getTypes()
    {
        $types = [];

        foreach (Contract::$types as $key => $type) {
            $types[] = [
                'id' => $key,
                'label' => $type,
            ];
        }

        return response($types);
    }

    public function getAvailableForCompany(Request $request)
    {
        return response([
            'status' => true,
            'contracts' => Contract::where('company_id', $request->company_id)->get(),
        ]);
    }

    public function getDriversByCompany($id)
    {
        return response(Driver::where('company_id', $id)->get());
    }

    public function getCarsByCompany($id)
    {
        return response(Car::where('company_id', $id)->get());
    }
}
