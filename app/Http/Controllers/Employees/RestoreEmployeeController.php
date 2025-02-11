<?php

namespace App\Http\Controllers\Employees;

use App\Actions\Employees\RestoreEmployee\RestoreEmployeeCommand;
use App\Actions\Employees\RestoreEmployee\RestoreEmployeeHandler;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class RestoreEmployeeController extends Controller
{
    public function __invoke(int $id, RestoreEmployeeHandler $handler)
    {
        DB::beginTransaction();

        try {
            $handler->handle(new RestoreEmployeeCommand($id));

            DB::commit();

            return response()->json()->setStatusCode(Response::HTTP_CREATED);
        } catch (ValidationException $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->errors(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}