<?php

namespace App\Http\Controllers\TripTickets;

use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\TripTicket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripTicketRelatedFormsController extends Controller
{
    public function __invoke(Request $request)
    {
        $tripTicket = TripTicket::where('uuid', '=', $request->input('id'))->first();
        $medic = null;
        $tech = null;

        if ($tripTicket->medic_form_id) {
            $medic = $this->getForm($tripTicket->medic_form_id, FormTypeEnum::MEDIC);
        }

        if ($tripTicket->tech_form_id) {
            $tech = $this->getForm($tripTicket->tech_form_id, FormTypeEnum::TECH);
        }

        return response()->json([
            'medic' => $medic,
            'tech' => $tech,
        ]);
    }

    private function getForm(string $id, string $type)
    {
        return Form::select([
                'forms.id',
                DB::raw("CONCAT('[', forms.id, '] ',DATE_FORMAT(forms.date, '%d.%m.%Y'), ' - ', type_view, ' - ', drivers.fio) AS text")
            ])
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
            ->findOrFail($id)
            ->toArray();
    }
}
