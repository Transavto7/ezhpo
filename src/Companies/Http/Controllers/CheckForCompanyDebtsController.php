<?php

namespace Src\Companies\Http\Controllers;

use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Companies\Commands\CheckForCompanyDebts\CheckForCompanyDebtsCommand;
use Src\Companies\Commands\CheckForCompanyDebts\CheckForCompanyDebtsHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckForCompanyDebtsController extends Controller
{
    public function __invoke(string $id, CheckForCompanyDebtsHandler $handler): JsonResponse
    {
        try {
            $company = Company::findOrFail($id);

            $debtStatus = $handler->handle(new CheckForCompanyDebtsCommand($company));

            return response()->json(['status' => $debtStatus->toArray()]);
        } catch (Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
