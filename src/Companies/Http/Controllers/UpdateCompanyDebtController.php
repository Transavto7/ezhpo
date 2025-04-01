<?php

namespace Src\Companies\Http\Controllers;

use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Src\Companies\Commands\UpdateCompanyDebt\UpdateCompanyDebtCommand;
use Src\Companies\Commands\UpdateCompanyDebt\UpdateCompanyDebtHandler;
use Src\Companies\Http\Requests\UpdateCompanyDebtRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateCompanyDebtController extends Controller
{
    public function __invoke($id, UpdateCompanyDebtHandler $handler, UpdateCompanyDebtRequest $request): JsonResponse
    {
        if (! user()->access('integration_1c_write')) {
            return response()->json([
                'message' => 'У пользователя нет доступа к API интеграции с 1С',
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $company = Company::withTrashed()->where('hash_id', $id)->first();

        if ($company === null) {
            return response()->json([
                'message' => 'Компания с таким HASH ID не существует!',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $handler->handle(new UpdateCompanyDebtCommand($company, $request->input('is_debt')));

            DB::commit();

            return response()->json(['message' => 'Статус задолженности компании успешно обновлен!']);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
