<?php

namespace Src\Companies\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CompanyReqsChecker\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Src\Companies\Commands\CheckForCompanyDebts\CheckForCompanyDebtsCommand;
use Src\Companies\Commands\CheckForCompanyDebts\CheckForCompanyDebtsHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckForCompanyDebtsController extends Controller
{
    public function __invoke(string $id, CheckForCompanyDebtsHandler $handler, CompanyRepository $repository): JsonResponse
    {
        try {
            $debtStatus = $handler->handle(new CheckForCompanyDebtsCommand($repository->findById($id)));

            return response()->json(['status' => $debtStatus->toArray()]);
        } catch (Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
