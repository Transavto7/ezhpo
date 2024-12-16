<?php

namespace App\Http\Controllers\TripTickets;

use App\FieldPrompt;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TripTicketIndexPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $type = TripTicket::SLUG;
        $user = Auth::user();
        $take = $request->get('take') ?? 100;
        $trash = filter_var($request->get('trash', 0), FILTER_VALIDATE_BOOLEAN);
        $orderKey = $request->get('orderKey', 'start_date');
        $orderBy = $request->get('orderBy', 'DESC');

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
            ->orderBy($orderKey, $orderBy);

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
}
