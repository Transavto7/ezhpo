<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\TripTicketsQuery\TripTicketsQueryAction;
use App\Actions\TripTicket\TripTicketsQuery\TripTicketsQueryHandler;
use App\Actions\TripTicket\TripTicketsTableExport\ExportTripTicketsTableHandler;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;

final class TripTicketTableExportController extends Controller
{
    public function __invoke(Request $request, TripTicketsQueryHandler $handler)
    {
        $trash = filter_var($request->get('trash', 0), FILTER_VALIDATE_BOOLEAN);
        $orderKey = $request->get('orderKey', 'start_date');
        $orderBy = $request->get('orderBy', 'DESC');
        $filterActivated = ! empty($request->get('filter'));
        $filterParams = $request->except([
            'trash',
            'export',
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

        if ($request->get('exportPrikaz', false)) {
            $tripTickets->addSelect([
                'cars.mark_model as car_name',
                'users.name as user_name',
                'users.eds as user_sign',
            ])
                ->leftJoin('users', 'users.id', '=', 'trip_tickets.user_id');
            $fields = $this->filter(collect(TripTicket::EXPORT_PRIKAZ_FIELDS));
            $title = 'Экспорт реестра ПЛ по приказу.xlsx';
        } else {
            $fields = $this->filter(collect(TripTicket::EXPORT_FIELDS));
            $title = 'Экспорт реестра ПЛ.xlsx';
        }

        $tripTickets = $tripTickets->get();

        try {
            return Excel::download(new ExportTripTicketsTableHandler($tripTickets, $fields), $title);
        } catch (Exception $e) {
            return response()
                ->json(['error' => $e->getMessage()])
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function filter(Collection $fields)
    {
        $exclude = [];
        if (Auth::user()->hasRole('client')) {
            $exclude = config('fields.client_exclude.trip_tickets') ?? [];
        }

        if (count($exclude) === 0) {
            return $fields;
        }

        return $fields->filter(function ($title, $field) use ($exclude) {
            return !in_array($field, $exclude);
        });
    }
}
