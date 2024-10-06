<?php

namespace App\Http\Controllers\Api\OneC;

use App\Company;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;

class GetCompaniesItemsController extends Controller
{
    public function __invoke()
    {
        try {
            $companies = Company::query()
                ->select([
                    'id',
                    'hash_id',
                    'name',
                    'inn',
                ])
                ->where('dismissed', '=', 'Нет')
                ->get();

            return response()->json($companies);
        } catch (Exception $exception) {
            return response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
