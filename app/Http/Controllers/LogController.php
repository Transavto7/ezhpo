<?php

namespace App\Http\Controllers;

use App\Enums\LogActionTypesEnum;
use App\Enums\LogModelTypesEnum;
use App\Log;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(): View
    {
        $actionTypes = LogActionTypesEnum::options()->toArray();

        $modelTypes = Log::query()
            ->select(['model_type'])
            ->distinct()
            ->get()
            ->pluck('model_type')
            ->map(function ($item) {
                return [
                    'id' => $item,
                    'text' => LogModelTypesEnum::label($item)
                ];
            })
            ->toArray();

        $users = User::query()
            ->select([
                'users.id as id',
                DB::raw("CONCAT('[',users.hash_id,'] ',users.name) as text")
            ])
            ->distinct()
            ->leftJoin('logs', 'logs.user_id', '=', 'users.id')
            ->whereNotNull('logs.user_id')
            ->get();

        return view(
            'admin.logs.index',
            compact('actionTypes', 'modelTypes', 'users')
        );
    }

    public function list(Request $request): JsonResponse
    {
        $data = Log::query()
            ->select([
                'logs.*',
                DB::raw("IF(ISNULL(users.hash_id), '-', CONCAT('[', users.hash_id, '] ', users.name)) as user")
            ])
            ->dateFrom($request->input('filter.date_start'))
            ->dateTo($request->input('filter.date_end'))
            ->modelTypes($request->input('filter.models'))
            ->modelId($request->input('filter.id'))
            ->userIds($request->input('filter.users'))
            ->actionTypes($request->input('filter.actions'))
            ->leftJoin('users', 'logs.user_id', '=', 'users.id')
            ->paginate(
                $request->input('limit', 100),
                ['*'],
                'page',
                $request->input('page', 1)
            );

        return response()->json($data);
    }
}
