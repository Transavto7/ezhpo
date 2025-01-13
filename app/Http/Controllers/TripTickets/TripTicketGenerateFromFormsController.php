<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\CreateTripTickets\TripTicketsAction;
use App\Actions\TripTicket\CreateTripTickets\TripTicketsHandler;
use App\Company;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketGenerateFromFormsController extends Controller
{
    public function __invoke(Request $request, TripTicketsHandler $handler)
    {
        if (! $request->has('date_from') || ! $request->has('date_to')) {
            return back()->with(['error' => 'Не выбран период ПЛ'])->withInput();
        }

        $startDate = Carbon::parse($request->input('date_from'));
        $endDate = Carbon::parse($request->input('date_to'));

        if ($endDate->diff($startDate)->days > 31) {
            return back()->with(['error' => 'Выбранный период ПЛ превышает 31 день'])->withInput();
        }

        if ($request->input('company_id')) {
            $company = Company::where('hash_id', '=', $request->input('company_id'))->first();

            if ($company === null) {
                return back()->with(['error' => "Компания с id {$request->input('company_id')} не найдена"])->withInput();
            }
        } else {
            return back()->with(['error' => 'Поле "Компания" обязательно для заполнения'])->withInput();
        }

        $driver = null;
        if ($request->input('driver_id')) {
            $driver = Driver::where('hash_id', '=', $request->input('driver_id'))->first();

            if ($driver === null) {
                return back()->with(['error' => "Водитель с id {$request->input('driver_id')} не найден"])->withInput();
            }
        }

        $ticketData = $request->input('trip_ticket')[0];

        try {
            DB::beginTransaction();
            $response['created'] = $handler->handle(new TripTicketsAction(
                $company,
                $driver,
                $startDate,
                $endDate,
                LogisticsMethodEnum::fromString($ticketData['logistics_method']),
                TransportationTypeEnum::fromString($ticketData['transportation_type']),
                TripTicketTemplateEnum::fromString($ticketData['template_code']),
                $ticketData['validity_period'] ?: 1
            ));
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $response['errors'] = [$exception->getMessage()];
        }

        if (array_key_exists('created', $response) && count($response['created']) === 0) {
            $response['errors'] = ['По заданным параметрам осмотры не найдены или они уже используются в других путевых листах'];
        }

        return back()->with($response);
    }
}
