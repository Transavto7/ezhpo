<?php

namespace App\Actions\TripTicket\TripTicketsQuery;

use App\Models\TripTicket;
use Illuminate\Database\Eloquent\Builder;

final class TripTicketsQueryHandler
{
    public function handle(TripTicketsQueryAction $action): Builder
    {
        if ($action->isTrash()) {
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
            ->orderBy($action->getOrderKey(), $action->getOrderBy());

        if (count($action->getFilterParams()) > 0 && $action->isFilterActivated()) {
            foreach ($action->getFilterParams() as $filterKey => $filterValue) {
                if ($filterValue === null || in_array($filterKey, ['date_from', 'date_to'])) {
                    continue;
                }

                $tripTickets->where("trip_tickets.$filterKey", '=', $filterValue);
            }

            if ($action->getFilterParams()['date_from'] || $action->getFilterParams()['date_to']) {
                $tripTickets = $tripTickets
                    ->whereBetween('start_date', [$action->getFilterParams()['date_from'], $action->getFilterParams()['date_to']]);
            }
        } else {
            $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');

            $tripTickets = $tripTickets
                ->whereBetween('start_date', [$date_from_filter, $date_to_filter]);
        }

        return $tripTickets;
    }
}
