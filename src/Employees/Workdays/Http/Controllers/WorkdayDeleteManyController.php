<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Src\Employees\Workdays\Eloquent\Workday;
use Throwable;

class WorkdayDeleteManyController extends Controller
{
    public function __invoke(Request $request, TrashWorkdayHandler $handler): JsonResponse
    {
        $ids = $request->input('ids') ?? [];
        $action = $request->input('action');
        $notDeletedItems = [];

        foreach ($ids as $id) {
            try {
                DB::beginTransaction();

                $item = Workday::withTrashed()->findOrFail($id);

                $handler->handle($item, $action, Auth::user());

                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();

                $notDeletedItems[] = $id;
            }
        }

        if (count($notDeletedItems)) {
            session()->flash('not_deleted_workdays', $notDeletedItems);
        }

        return response()->json();
    }
}
