<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\TripTicketsQuery\TripTicketsQueryAction;
use App\Actions\TripTicket\TripTicketsQuery\TripTicketsQueryHandler;
use App\FieldPrompt;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TripTicketIndexPageController extends Controller
{
    public function __invoke(Request $request, TripTicketsQueryHandler $handler)
    {
        $type = TripTicket::SLUG;
        $user = Auth::user();
        $take = $request->get('take') ?? 100;
        $trash = filter_var($request->get('trash', 0), FILTER_VALIDATE_BOOLEAN);
        $orderKey = $request->get('orderKey', 'start_date');
        $orderBy = $request->get('orderBy', 'DESC');
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

        $tripTickets = $handler->handle(new TripTicketsQueryAction(
            $trash,
            $orderKey,
            $orderBy,
            $filterActivated,
            $filterParams
        ));

        $fieldPrompts = FieldPrompt::query()
            ->where('type', $type)
            ->orderBy('sort')
            ->orderBy('id')
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