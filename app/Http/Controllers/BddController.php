<?php

namespace App\Http\Controllers;

use App\Actions\Anketa\CreateBddFormHandler;
use App\Company;
use App\Driver;
use App\Instr;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BddController extends Controller
{
    public function get(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $briefings = Instr::where('active', 1)
            ->orderBy('sort', 'asc')
            ->get();

        $driver = Driver::where('hash_id', $user->id)->first();

        if ($driver) {
            $company = Company::find($driver->company_id);

            $pv_id = $company ? $company->pv_id : 0;
        } else {
            $pv_id = 0;
        }

        $nullable = $briefings->where('sort','===', null);
        $sorted = $briefings->where('sort','!==' ,null)->sortBy('sort');

        $briefings = collect();
        $briefings = $briefings->merge($sorted);
        $briefings = $briefings->merge($nullable);

        return view('pages.driver_bdd', [
            'instrs' => $briefings,
            'pv_id' => $pv_id
        ]);
    }

    public function store(Request $request, CreateBddFormHandler $handler): JsonResponse
    {
        try {
            DB::beginTransaction();

            $responseData = $handler->handle($request->all(), Auth::user());

            DB::commit();

            return response()->json($responseData);
        } catch (Throwable $exception) {
            DB::rollBack();

            $responseData = [
                'errors' => [$exception->getMessage()],
            ];

            return response()->json($responseData);
        }
    }
}
