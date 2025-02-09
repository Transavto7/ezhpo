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
                DB::raw("CONCAT('[', forms.id, '] ',DATE_FORMAT(forms.date, '%d.%m.%Y'), ' - ', type_view, ' - ', drivers.fio) AS text")
            ])
            ->whereRaw( "not exists (
                select * from trip_tickets
                where forms.id = trip_tickets.tech_form_id
                and trip_tickets.deleted_at is null"
                .($tripTicket->medic_form_id ? " and trip_tickets.medic_form_id != $tripTicket->medic_form_id " : ' ')
                .($tripTicket->tech_form_id ? " and trip_tickets.tech_form_id != $tripTicket->tech_form_id " : ' ')
                .")")
            ->when($type === FormTypeEnum::MEDIC, function (Builder $query) {
                $query->leftJoin('medic_forms',
                    'forms.uuid',
                    '=',
                    'medic_forms.forms_uuid');
            })
            ->when($type === FormTypeEnum::TECH, function (Builder $query) {
                $query->leftJoin('tech_forms',
                    'forms.uuid',
                    '=',
                    'tech_forms.forms_uuid');
            })
            ->leftJoin('drivers',
                'forms.driver_id',
                '=',
                'drivers.hash_id')
            ->when($term, function (Builder $query) use ($term) {
                $query->where('id', 'like', '%' . $term . '%');
            })
            ->where('forms.company_id', '=', $tripTicket->company_id)
            ->whereNotNull('forms.driver_id')
            ->where('forms.type_anketa', '=', $type)
            ->when($tripTicket->driver_id, function (Builder $query) use ($tripTicket) {
                $query->where('forms.driver_id', '=', $tripTicket->driver_id);
            })
            ->when($tripTicket->start_date, function (Builder $query) use ($tripTicket) {
                $query->where('forms.date', '>=', $tripTicket->start_date)
                    ->where('forms.date', '<=', Carbon::parse($tripTicket->start_date)->addDay());
            });

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $results->items(),
            'more' => $results->hasMorePages()
        ]);
    }
}
