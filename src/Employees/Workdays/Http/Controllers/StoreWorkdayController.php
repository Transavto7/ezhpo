<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Http\Controllers\Controller;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Employees\Workdays\Commands\StoreWorkdayManually\StoreWorkdayCommand;
use Src\Employees\Workdays\Commands\StoreWorkdayManually\StoreWorkdayHandler;
use Throwable;

final class StoreWorkdayController extends Controller
{
    public function __invoke(Request $request, StoreWorkdayHandler $handler)
    {
        DB::beginTransaction();

        $responseData = [];

        try {
            session(['anketa_pv_id' => [
                'value' => $request->get('pv_id', 0),
                'expired' => date('d.m')
            ]]);

            $workday = $handler->handle(new StoreWorkdayCommand(
                $request->input('employee_id'),
                $request->input('pv_id'),
                new DateTimeImmutable($request->input('date')),
                $request->input('type_anketa')
            ));

            $responseData['created'] = [$workday];

            DB::commit();
        } catch (Throwable $exception) {
            $responseData['errors'] = [$exception->getMessage()];

            DB::rollBack();
        }

        return back()->with($responseData);
    }
}
