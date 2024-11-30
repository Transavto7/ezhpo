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

        $fieldsKeys = TripTicket::FIELDS;
        $formsFields = array_keys($fieldsKeys);

        $fieldPrompts = FieldPrompt::query()
            ->where('type', $type)
            ->orderBy('sort')
            ->orderBy('id')
            ->whereNotIn('field', ['hour_from', 'hour_to'])
            ->get();

        $formsCountResult = $tripTickets->total();

        $orderKey = $request->get('orderKey', 'date');
        $orderBy = $request->get('orderBy', 'ASC');

        return view('home', [
            'title' => TripTicket::TITLE,
            'name' => $user->name,
            'ankets' => $tripTickets,
            'filter_activated' => $filterActivated,
            'type_ankets' => $type,
            'anketsFields' => $formsFields,
            'anketsFieldsTable' => TripTicket::TABLE_FIELD_KEYS,
            'fieldsKeys' => $fieldsKeys,
            'fieldPrompts' => $fieldPrompts,
            'fieldsGroupFirst' => TripTicket::FILTERS,
            'blockedToExportFields' => [],
            'anketasCountResult' => $formsCountResult,
            'typePrikaz' => $request->get('typePrikaz'),
            'currentRole' => $type,
            'take' => $take,
            'orderBy' => $orderBy,
            'orderKey' => $orderKey,
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }

    public function getFilters(Request $request)
    {
        $fields = TripTicket::TABLE_FIELD_KEYS;
        $fieldsGroupFirst = TripTicket::FILTERS;

        return view('home_filters', [
            'anketsFields' => $fields,
            'type_ankets' => TripTicket::SLUG,
            'fieldsKeys' => $fieldsGroupFirst,
            'fieldsGroupFirst' => $fieldsGroupFirst,
            'exclude' => []
        ]);
    }

    public function list(Request $request)
    {
        return response()->json();
    }
}
