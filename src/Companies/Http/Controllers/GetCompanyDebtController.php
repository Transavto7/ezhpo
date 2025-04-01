<?php

namespace Src\Companies\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CompanyReqsChecker\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Src\Companies\Commands\GetCompanyDebt\GetCompanyDebtCommand;
use Src\Companies\Commands\GetCompanyDebt\GetCompanyDebtHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GetCompanyDebtController extends Controller
{
    public function __invoke(string $id, GetCompanyDebtHandler $handler, CompanyRepository $repository): JsonResponse
    {
        try {
            $companyDebt = $handler->handle(new GetCompanyDebtCommand($repository->findById($id)));

            return response()->json(['debt' => $companyDebt->toArray()]);
        } catch (Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
