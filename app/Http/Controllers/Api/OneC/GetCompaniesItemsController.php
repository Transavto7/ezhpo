<?php

namespace App\Http\Controllers\Api\OneC;

use App\Company;
use App\Http\Controllers\Controller;
use Exception;
use Symfony\Component\HttpFoundation\Response;

final class GetCompaniesItemsController extends Controller
{
    public function __invoke()
    {
        if (!user()->access('integration_1c_read')) {
            return response()->json([
                'message' => 'Forbidden'
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        try {
            $companies = Company::query()
                ->select([
                    'id',
                    'hash_id',
                    'name',
                    'inn',
                    'req_id',
                ])
                ->where('dismissed', '<>', 'Да')
                ->get();

            return response()->json($companies);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
