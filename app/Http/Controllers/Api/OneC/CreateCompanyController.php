<?php

namespace App\Http\Controllers\Api\OneC;

use App\Actions\Element\CreateElementHandlerFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateCompanyController extends Controller
{
    public function __invoke(Request $request, CreateElementHandlerFactory $factory): JsonResponse
    {
        try {
            DB::beginTransaction();

            $companyId = $factory->make('Company')->handle($request->all());

            DB::commit();
            return response()->json([
                'id' => $companyId
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response()->json($exception->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
