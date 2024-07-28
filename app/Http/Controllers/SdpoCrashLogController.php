<?php

namespace App\Http\Controllers;

use App\Enums\SdpoCrashTypesEnum;
use App\Point;
use App\SdpoCrashLog;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SdpoCrashLogController extends Controller
{
    public function index(): View
    {
        $types = SdpoCrashTypesEnum::options()->toArray();

        $versions = SdpoCrashLog::query()
            ->select([
                'version'
            ])
            ->distinct()
            ->get()
            ->pluck('version')
            ->map(function ($item) {
                return [
                    'id' => $item,
                    'text' => $item
                ];
            })
            ->toArray();

        $points = Point::query()
            ->select([
                'points.id as id',
                DB::raw("CONCAT('[',towns.name,'] ',points.name) as text"),
            ])
            ->leftJoin('towns', 'towns.id', '=', 'points.pv_id')
            ->leftJoin('sdpo_crash_logs', 'sdpo_crash_logs.point_id', '=', 'points.id')
            ->whereNotNull('sdpo_crash_logs.point_id')
            ->get()
            ->toArray();

        $terminals = User::query()
            ->select([
                'users.id as id',
                DB::raw("CONCAT('[',users.hash_id,'] ',users.name) as text")
            ])
            ->leftJoin('sdpo_crash_logs', 'sdpo_crash_logs.terminal_id', '=', 'users.id')
            ->whereNotNull('sdpo_crash_logs.terminal_id')
            ->get()
            ->toArray();

        return view(
            'admin.sdpo-crash-logs.index',
            compact('types', 'versions', 'points', 'terminals')
        );
    }

    public function list(Request $request): JsonResponse
    {
        $data = SdpoCrashLog::query()
            ->select([
                'sdpo_crash_logs.*',
                DB::raw("CONCAT('[', users.hash_id, '] ', users.name) as terminal"),
                DB::raw("CONCAT('[',towns.name,'] ',points.name) as point")
            ])
            ->createdAtFrom($request->input('filter.created_at_start'))
            ->createdAtTo($request->input('filter.created_at_end'))
            ->happenedAtFrom($request->input('filter.happened_at_start'))
            ->happenedAtTo($request->input('filter.happened_at_end'))
            ->uuid($request->input('filter.uuid'))
            ->types($request->input('filter.types'))
            ->points($request->input('filter.points'))
            ->versions($request->input('filter.versions'))
            ->terminals($request->input('filter.terminals'))
            ->leftJoin('users', 'sdpo_crash_logs.terminal_id', '=', 'users.id')
            ->leftJoin('points', 'sdpo_crash_logs.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->orderBy('sdpo_crash_logs.happened_at', 'desc')
            ->paginate(
                $request->input('limit', 100),
                ['*'],
                'page',
                $request->input('page', 1)
            );

        return response()->json($data);
    }
}
