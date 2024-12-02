<?php

namespace App\Http\Controllers;

use App\FieldPrompt;
use App\Models\TripTicket;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripTicketController extends Controller
{
    public function indexPage(Request $request)
    {
        $type = TripTicket::SLUG;
        $user = Auth::user();
        $take = $request->get('take') ?? 100;

        $tripTickets = TripTicket::query()->paginate($take);

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

    public function generate()
    {

    }

    public function list(Request $request)
    {
        return response()->json();
    }
}
