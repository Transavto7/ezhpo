<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Actions\Anketa\TrashFormHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Src\Employees\Workdays\Eloquent\Workday;
use Throwable;

class WorkdayDeleteController extends Controller
{
    public function __invoke(Request $request, TrashFormHandler $handler)
    {
        $id = $request->id;
        $action = $request->action;
        $item = Workday::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();

            $handler->handle($item, $action, Auth::user());

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            session()->flash('not_deleted_workdays', [$id]);
        }

        return redirect(url()->previous());
    }
}
