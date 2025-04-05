<?php

namespace App\Http\Controllers\TripTickets;

use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\TripTicket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripTicketSelectFormsController extends Controller
{
    public function __invoke(Request $request)
    {
        $id = $request->input('currentTripTicketId');
        $term = $request->input('term');
        $page = $request->input('page', 1);
        $type = $request->input('type');

        $perPage = 30;

        $tripTicket = TripTicket::where('uuid', '=', $id)->first();

        $query = Form::select([
                'forms.id',
                DB::raw("CONCAT(
                    '[', forms.id, '] ',
                    CASE
                        WHEN forms.date IS NULL
                        THEN CONCAT(period_pl, ' - ')
                        ELSE CONCAT(DATE_FORMAT(forms.date, '%d.%m.%Y'), ' - ')
                    END,
                    type_view, ' - ', drivers.fio
                ) AS text")
            ])
            ->whereRaw("not exists (
                select * from trip_tickets
                where (forms.id = trip_tickets.tech_form_id or forms.id = trip_tickets.medic_form_id)
                and trip_tickets.deleted_at is null"
                .($tripTicket->medic_form_id ? " and trip_tickets.medic_form_id != $tripTicket->medic_form_id " : ' ')
                .($tripTicket->tech_form_id ? " and trip_tickets.tech_form_id != $tripTicket->tech_form_id " : ' ')
                .")"
            )
            ->where('forms.type_anketa', '=', $type)
            ->when($type === FormTypeEnum::MEDIC, function (Builder $query) use ($tripTicket) {
                $query->leftJoin('medic_forms',
                    'forms.uuid',
                    '=',
                    'medic_forms.forms_uuid')
                    ->where(function (Builder $query) use ($tripTicket) {
                        $query->where('medic_forms.is_dop', '=', 0)
                            ->orWhere(function (Builder $query) {
                                $query->where('medic_forms.is_dop', '=', 1)
                                    ->whereNotNull('medic_forms.result_dop');
                            })
                            ->when($tripTicket->medic_form_id, function (Builder $query) use ($tripTicket) {
                                $query->orWhere('forms.id', '=', $tripTicket->medic_form_id);
                            });
                    });
            })
            ->when($type === FormTypeEnum::TECH, function (Builder $query) use ($tripTicket) {
                $query->leftJoin('tech_forms',
                    'forms.uuid',
                    '=',
                    'tech_forms.forms_uuid')
                    ->when($tripTicket->car_id, function (Builder $subquery) use ($tripTicket) {
                        $subquery->where('car_id', '=', $tripTicket->car_id);
                    })
                    ->where(function (Builder $query) use ($tripTicket) {
                        $query->where('tech_forms.is_dop', '=', 0)
                            ->orWhere(function (Builder $query) use ($tripTicket) {
                                $query->where('tech_forms.is_dop', '=', 1)
                                    ->whereNotNull('tech_forms.result_dop');
                            })
                            ->when($tripTicket->tech_form_id, function (Builder $query) use ($tripTicket) {
                                $query->orWhere('forms.id', '=', $tripTicket->tech_form_id);
                            });
                    });
            })
            ->leftJoin('drivers',
                'forms.driver_id',
                '=',
                'drivers.hash_id')
            ->when($term, function (Builder $query) use ($term) {
                $query->where('forms.id', 'like', '%' . $term . '%');
            })
            ->where('forms.company_id', '=', $tripTicket->company_id)
            ->whereNotNull('forms.driver_id')
            ->when($tripTicket->driver_id, function (Builder $query) use ($tripTicket) {
                $query->where('forms.driver_id', '=', $tripTicket->driver_id);
            })
            ->when($tripTicket->start_date, function (Builder $query) use ($tripTicket) {
                $query->where('forms.date', '>=', $tripTicket->start_date)
                    ->where('forms.date', '<=', Carbon::parse($tripTicket->start_date)->addDay());
            })
            ->when(! $tripTicket->start_date && $tripTicket->period_pl, function (Builder $query) use ($tripTicket) {
                $period = Carbon::parse($tripTicket->period_pl);
                $query->where('forms.date', '>=', $period->startOfMonth()->format('Y-m-d H:i:s'))
                    ->where('forms.date', '<=', $period->endOfMonth()->format('Y-m-d H:i:s'));
            });

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $results->items(),
            'more' => $results->hasMorePages()
        ]);
    }
}
