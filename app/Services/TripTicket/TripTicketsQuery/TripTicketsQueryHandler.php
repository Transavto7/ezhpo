<?php

namespace App\Services\TripTicket\TripTicketsQuery;

use App\Enums\TripTicket\TripTicketStatus;
use App\Enums\TripTicket\TripTicketType;
use App\Models\TripTicket;
use Carbon\Carbon;
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
            'trip_tickets.external_number',
            'trip_tickets.start_date',
            'trip_tickets.period_pl',
            'trip_tickets.validity_period',
            'trip_tickets.medic_form_id',
            'trip_tickets.tech_form_id',
            'trip_tickets.logistics_method',
            'trip_tickets.transportation_type',
            'trip_tickets.template_code',
            'trip_tickets.photos',
            'trip_tickets.status',
            'trip_tickets.type',
            'trip_tickets.created_at',
            'trip_tickets.deleted_at',

            'companies.name as company_name',
            'drivers.fio as driver_name',
            'cars.gos_number as car_number',
            'employees.name as user_name',
            'deleted_employees.name as deleted_user_name',
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
            ->leftJoin(
                'employees as deleted_employees',
                'deleted_employees.related_user_id',
                '=',
                'trip_tickets.deleted_id'
            )
            ->leftJoin(
                'employees',
                'employees.id',
                '=',
                'trip_tickets.employee_id'
            )
            ->orderBy($action->getOrderKey(), $action->getOrderBy());

        $user = \Auth::user();
        if ($user->isCompany()) {
            $tripTickets->where('trip_tickets.company_id', $user->relatedCompany->hash_id);
        }

        $dateFrom = isset($action->getFilterParams()['date_from'])
            ? Carbon::parse($action->getFilterParams()['date_from'])
            : Carbon::now()->subYears(10);
        $dateTo = isset($action->getFilterParams()['date_to'])
            ? Carbon::parse($action->getFilterParams()['date_to'])
            : Carbon::now()->addYears(10);

        $createdDateFrom = isset($action->getFilterParams()['created_date_from'])
            ? Carbon::parse($action->getFilterParams()['created_date_from'])
            : null;
        $createdDateTo = isset($action->getFilterParams()['created_date_to'])
            ? Carbon::parse($action->getFilterParams()['created_date_to'])
            : null;

        if (count($action->getFilterParams()) > 0 && $action->isFilterActivated()) {
            foreach ($action->getFilterParams() as $filterKey => $filterValue) {
                if ($filterValue === null || in_array($filterKey, ['date_from', 'date_to', 'created_date_from', 'created_date_to'])) {
                    continue;
                }

                if ($filterKey === 'has_photos') {
                    if (filter_var($filterValue, FILTER_VALIDATE_BOOLEAN)) {
                        $tripTickets->whereNotNull('trip_tickets.photos');
                    } else {
                        $tripTickets->whereNull('trip_tickets.photos');
                    }

                    continue;
                }

                if ($filterKey === 'is_approved') {
                    $isApproved = filter_var($filterValue, FILTER_VALIDATE_BOOLEAN);

                    $tripTickets->where(function (Builder $query) use ($isApproved) {
                        $query->where(function (Builder $query) use ($isApproved) {
                            $query->where('type', '=', TripTicketType::GENERATED)
                                ->where('status', $isApproved ? '!=' : '=', TripTicketStatus::CREATED);
                        })->orWhere(function (Builder $query) use ($isApproved) {
                            $query->where(function (Builder $query) use ($isApproved) {
                                $query->where('type', '=', TripTicketType::IN_ADVANCE)
                                    ->where('status', $isApproved ? '=' : '!=', TripTicketStatus::APPROVED);
                            });
                        })->orWhere(function (Builder $query) use ($isApproved) {
                            $query->where(function (Builder $query) use ($isApproved) {
                                $query->where('type', '=', TripTicketType::COMMON)
                                    ->where('status', $isApproved ? '!=' : '=', TripTicketStatus::CREATED);
                            });
                        });
                    });

                    continue;
                }

                $tripTickets->where("trip_tickets.$filterKey", '=', $filterValue);
            }
        }

        return $tripTickets->where(function ($query) use ($dateFrom, $dateTo) {
            $query->where(function ($subQuery) use ($dateFrom, $dateTo) {
                $subQuery->whereNotNull('trip_tickets.start_date')
                    ->whereBetween('trip_tickets.start_date', [$dateFrom, $dateTo]);
            })
                ->orWhere(function ($subQuery) use ($dateFrom, $dateTo) {
                    $subQuery
                        ->whereNull('trip_tickets.start_date')
                        ->whereBetween('trip_tickets.period_pl', [
                            $dateFrom->format('Y-m'),
                            $dateTo->format('Y-m')
                        ]);
                });
            })
            ->when($createdDateFrom, function ($query) use ($createdDateFrom) {
                $query->where('trip_tickets.created_at', '>=', $createdDateFrom->startOfDay());
            })
            ->when($createdDateTo, function ($query) use ($createdDateTo) {
                $query->where('trip_tickets.created_at', '<=', $createdDateTo->endOfDay());
            });
    }
}
