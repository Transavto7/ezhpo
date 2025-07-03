<?php

declare(strict_types=1);

namespace App\Http\Controllers\Terminals;

use App\Actions\Terminals\SetManyTerminalsDescription\SetManyTerminalsDescriptionCommand;
use App\Actions\Terminals\SetManyTerminalsDescription\SetManyTerminalsDescriptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class SetManyTerminalsDescriptionController
{
    public function __invoke(Request $request, SetManyTerminalsDescriptionHandler $handler): JsonResponse
    {
        DB::beginTransaction();

        try {
            $handler->handle(new SetManyTerminalsDescriptionCommand(
                $request->input('ids', []),
                $request->input('description'),
            ));

            DB::commit();

            return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return response()
                ->json([
                    'errors' => [$exception->getMessage()],
                ])
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
