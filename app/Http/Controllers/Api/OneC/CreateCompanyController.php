<?php

namespace App\Http\Controllers\Api\OneC;

use App\Actions\Element\CreateElementHandlerFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;
use Validator;

class CreateCompanyController extends Controller
{
    public function __invoke(Request $request, CreateElementHandlerFactory $factory): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'req_id' => 'required|string',
                'inn' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(),Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $companyId = $factory->make('Company')->handle($request->all());

            DB::commit();
            return response()->json([
                'id' => $companyId
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            if ($exception->getMessage() === 'Найден дубликат по названию компании') {
                return response()->json($exception->getMessage(),Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return response()->json($exception->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
