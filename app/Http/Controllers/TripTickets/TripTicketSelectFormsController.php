<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\TripTicket;
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
                'id',
                DB::raw("CONCAT('[', id, '] ',DATE_FORMAT(date, '%d.%m.%Y'), ' - Водитель - ', driver_id) AS text")
            ])
            ->leftJoin('medic_forms',
                'forms.uuid',
                '=',
                'medic_forms.forms_uuid')
            ->leftJoin('tech_forms',
                'forms.uuid',
                '=',
                'tech_forms.forms_uuid')
            ->when($term, function (Builder $query) use ($term) {
                $query->where('id', 'like', '%' . $term . '%');
            })
            ->where('forms.company_id', '=', $tripTicket->company_id)
            ->where('forms.type_anketa', '=', $type)
            ->when($tripTicket->driver_id, function (Builder $query) use ($tripTicket) {
                $query->where('forms.driver_id', '=', $tripTicket->driver_id);
            })
            ->when($tripTicket->start_date, function (Builder $query) use ($tripTicket) {
                $query->where('forms.date', '=', $tripTicket->start_date);
            });

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $results->items(),
            'more' => $results->hasMorePages()
        ]);
    }
}
