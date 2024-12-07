<?php

namespace App\Http\Controllers;

use App\Actions\TripTicket\CreateTripTickets\TripTicketsAction;
use App\Actions\TripTicket\CreateTripTickets\TripTicketsHandler;
use App\Actions\TripTicket\DeleteTripTickets\TrashTripTicketHandler;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketAction;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketHandler;
use App\Company;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\FieldPrompt;
use App\Models\TripTicket;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketController extends Controller
{
    public function indexPage(Request $request, $tripTicketIds = null)
    {
        $type = TripTicket::SLUG;
        $user = Auth::user();
        $take = $request->get('take') ?? 100;
        $trash = filter_var($request->get('trash', 0), FILTER_VALIDATE_BOOLEAN);

        if ($trash) {
            $tripTickets = TripTicket::onlyTrashed();
        } else {
            $tripTickets = TripTicket::query();
        }

        $tripTickets = $tripTickets->select([
                'trip_tickets.id',
                'trip_tickets.uuid',
                'trip_tickets.ticket_number',
                'trip_tickets.start_date',
                'trip_tickets.validity_period',
                'trip_tickets.medic_form_id',
                'trip_tickets.tech_form_id',
                'trip_tickets.logistics_method',
                'trip_tickets.transportation_type',
                'trip_tickets.template_code',
                'trip_tickets.created_at',

                'companies.name as company_name',
                'drivers.fio as driver_name',
                'cars.gos_number as car_number',
            ])
            ->leftJoin(
                'companies',
                'companies.hash_id',
                '=',
                'trip_tickets.company_id',
            )
            ->leftJoin(
                'drivers',
                'drivers.hash_id',
                '=',
                'trip_tickets.driver_id',
            )
            ->leftJoin(
                'cars',
                'cars.hash_id',
                '=',
                'trip_tickets.car_id',
            )
            ->when($tripTicketIds !== null, function ($query) use ($tripTicketIds) {
                $query->whereIn('uuid', $tripTicketIds);
            });

        $filterActivated = ! empty($request->get('filter'));
        $filterParams = $request->except([
            'trash',
            'export',
            'exportPrikazPL',
            'exportPrikaz',
            'filter',
            'take',
            'orderBy',
            'orderKey',
            'page',
        ]);

        if (count($filterParams) > 0 && $filterActivated) {
            foreach ($filterParams as $filterKey => $filterValue) {
                if ($filterValue === null || in_array($filterKey, ['date_from', 'date_to'])) {
                    continue;
                }

                $tripTickets->where("trip_tickets.$filterKey", '=', $filterValue);
            }

            if ($filterParams['date_from'] || $filterParams['date_to']) {
                $tripTickets = $tripTickets
                    ->whereBetween('start_date', [$filterParams['date_from'], $filterParams['date_to']]);
            }
        } else {
            $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');

            $tripTickets = $tripTickets
                ->whereBetween('start_date', [$date_from_filter, $date_to_filter]);
        }

        $fieldPrompts = FieldPrompt::query()
            ->where('type', $type)
            ->orderBy('sort')
            ->orderBy('id')
            ->whereNotIn('field', ['hour_from', 'hour_to'])
            ->get();

        $tripTickets = $tripTickets->paginate($take);
        $countResult = $tripTickets->total();

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
            'tripTicketsCountResult' => $countResult,
            'take' => $take,
            'orderBy' => $orderBy,
            'orderKey' => $orderKey,
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }

    public function createPage()
    {
        date_default_timezone_set('UTC');
        $time = time();
        $user = Auth::user();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d', $time);

        return view('trip-tickets.create', [
            'title' => 'Создание путевого листа',
            'default_current_date' => $time
        ]);
    }

    public function store(Request $request, StoreTripTicketHandler $handler)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $response = $handler->handle(new StoreTripTicketAction(
                $request->input('company_id'),
                $request->input('driver_id'),
                $request->input('car_id'),
                $request->input('start_date'),
                $request->input('additional_dates')
                    ? explode(', ', $request->input('additional_dates'))
                    : [],
                $request->input('validity_period', 1),
                $request->input('ticket_number'),
                LogisticsMethodEnum::fromString($request->input('logistics_method')),
                TransportationTypeEnum::fromString($request->input('transportation_type')),
                TripTicketTemplateEnum::fromString($request->input('template_code')),
            ));

            DB::commit();
        } catch (Throwable $exception) {
            $response['errors'] = [$exception->getMessage()];

            DB::rollBack();
        }

        return back()->with($response);
    }

    public function editPage()
    {

    }

    public function trash(Request $request, TrashTripTicketHandler $handler)
    {
        $id = $request->input('id');
        $action = $request->input('action');
        $tripTicket = TripTicket::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();

            $handler->handle($tripTicket, $action, Auth::user());

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            session()->flash('not_deleted_items', [$id]);
        }

        return redirect(url()->previous());
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
            $company,
            $driver,
            $startDate,
            $endDate,
            LogisticsMethodEnum::fromString($request->input('logistics_method')),
            TransportationTypeEnum::fromString($request->input('transportation_type')),
            TripTicketTemplateEnum::fromString($request->input('template_code')),
            $request->input('validity_period', 1)
        ));

        return $this->indexPage($request, $tripTicketIds);
    }

    public function massTrash(Request $request, TrashTripTicketHandler $handler): JsonResponse
    {
        $ids = $request->input('ids') ?? [];
        $action = $request->input('action');
        $notDeleted = [];

        foreach ($ids as $id) {
            try {
                DB::beginTransaction();

                $tripTicket = TripTicket::withTrashed()->findOrFail($id);
                $handler->handle($tripTicket, $action, Auth::user());

                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();

                $notDeleted[] = $id;
            }
        }

        if (count($notDeleted)) {
            session()->flash('not_deleted_items', $notDeleted);
        }

        return response()->json();
    }
}
