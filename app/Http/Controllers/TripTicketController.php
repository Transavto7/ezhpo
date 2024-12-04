<?php

namespace App\Http\Controllers;

use App\Company;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\FieldPrompt;
use App\Models\TripTicket;
use App\Services\TripTicket\TripTicketsAction;
use App\Services\TripTicket\TripTicketsHandler;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripTicketController extends Controller
{
    public function indexPage(Request $request, $tripTicketIds = null)
    {
        $type = TripTicket::SLUG;
        $user = Auth::user();
        $take = $request->get('take') ?? 100;

        $tripTickets = TripTicket::query()
            ->when($tripTicketIds !== null, function ($query) use ($tripTicketIds) {
                $query->whereIn('uuid', $tripTicketIds);
            })
            ->paginate($take);

        $filterActivated = ! empty($request->get('filter'));

        $fieldPrompts = FieldPrompt::query()
            ->where('type', $type)
            ->orderBy('sort')
            ->orderBy('id')
            ->whereNotIn('field', ['hour_from', 'hour_to'])
            ->get();

        $formsCountResult = $tripTickets->total();

        $orderKey = $request->get('orderKey', 'date');
        $orderBy = $request->get('orderBy', 'ASC');

        $filters = TripTicket::FILTERS;

        return view('trip-tickets.index', [
            'filters' => $filters,
            'exclude' => [],
            'name' => $user->name,
            'tripTickets' => $tripTickets,
            'filter_activated' => $filterActivated,
            'fieldPrompts' => $fieldPrompts,
            'blockedToExportFields' => [],
            'tripTicketsCountResult' => $formsCountResult,
            'take' => $take,
            'orderBy' => $orderBy,
            'orderKey' => $orderKey,
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }

    public function editPage()
    {

    }

    public function trash()
    {

    }

    public function generate(Request $request, TripTicketsHandler $handler)
    {
        if (! $request->has('date_from') || ! $request->has('date_to')) {
            $request->session()->flash('error', 'Не выбран период ПЛ');
            return redirect(url()->previous());
        }

        $startDate = Carbon::parse($request->input('date_from'));
        $endDate = Carbon::parse($request->input('date_to'));

        if ($endDate->diff($startDate)->days > 31) {
            $request->session()->flash('error', 'Выбранный период ПЛ превышает 31 день');
            return redirect(url()->previous());
        }

        if ($request->input('company_id')) {
            $company = Company::where('hash_id', '=', $request->input('company_id'))->first();

            if ($company === null) {
                $request->session()->flash('error', "Компания с id {$request->input('company_id')} не найдена");
                return redirect(url()->previous());
            }
        } else {
            $request->session()->flash('error', 'Поле "Компания" обязательно для заполнения');
            return redirect(url()->previous());
        }

        $driver = null;
        if ($request->input('driver_id')) {
            $driver = Driver::where('hash_id', '=', $request->input('driver_id'))->first();

            if ($driver === null) {
                $request->session()->flash('error', "Водитель с id {$request->input('driver_id')} не найден");
                return redirect(url()->previous());
            }
        }

        $tripTicketIds = $handler->handle(new TripTicketsAction(
            $company->hash_id,
            $driver
                ? $driver->hash_id
                : null,
            $startDate,
            $endDate,
            LogisticsMethodEnum::fromString($request->input('logistics_method')),
            TransportationTypeEnum::fromString($request->input('transportation_type')),
            TripTicketTemplateEnum::fromString($request->input('template_code')),
        ));

        return $this->indexPage($request, $tripTicketIds);
    }

    public function list(Request $request)
    {
        return response()->json();
    }
}
