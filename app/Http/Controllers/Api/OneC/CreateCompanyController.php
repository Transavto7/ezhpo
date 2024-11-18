<?php

namespace App\Http\Controllers\Api\OneC;

use App\Actions\Element\CreateCompanyHandler;
use App\Actions\Element\CreateElementHandlerFactory;
use App\Actions\User\CreateUserHandler;
use App\Exceptions\EntityAlreadyExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateCompanyController extends Controller
{
    public function __invoke(CreateCompanyRequest $request, CreateElementHandlerFactory $factory): JsonResponse
    {
        if (!user()->access('integration_1c_write')) {
            return response()->json([
                'message' => 'Forbidden'
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            $handler = new CreateCompanyHandler();

            $company = $handler->handle([
                'name' => $request->input('name'),
                'req_id' => $request->input('req_id'),
                'inn' => $request->input('inn'),
            ]);

            DB::commit();

            return response()->json([
                'id' => $company->id,
                'hash_id' => $company->hash_id,
            ])->setStatusCode(Response::HTTP_CREATED);
        } catch (EntityAlreadyExistException $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage().' '.$exception->getCode()
            ],Response::HTTP_CONFLICT);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
