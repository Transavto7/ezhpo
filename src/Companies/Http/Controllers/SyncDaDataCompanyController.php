<?php

namespace Src\Companies\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Companies\Actions\SyncReqs\SyncReqsCommand;
use Src\Companies\Actions\SyncReqs\SyncReqsHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SyncDaDataCompanyController extends Controller
{
    public function __invoke($id, SyncReqsHandler $handler): JsonResponse
    {
        try {
            $handler->handle(new SyncReqsCommand($id));

            return response()->json(['message' => 'Реквизиты компании успешно синхронизированы']);
        } catch (Throwable $exception) {
            throw $exception;

            return response()->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
