<?php

namespace App\Services\TripTicket;

use App\Enums\FormTypeEnum;
use App\Models\TripTicket;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;

class TripTicketsHandler
{
    public function handle(TripTicketsAction $action): array
    {
        $tripTicketIds = [];

        for ($date = $action->getDateFrom(); $action->getDateFrom() <= $action->getDateTo(); $action->getDateFrom()->addDay()) {
            $medicForms = $this->getMedicForms($action, $date);
            $techForms = $this->getTechForms($action, $date);

            $maxLength = max(count($medicForms), count($techForms));

            for ($i = 0; $i < $maxLength; $i++) {
                $medicForm = $medicForms[$i] ?? null;
                $techForm = $techForms[$i] ?? null;

                $number = $techForm
                    ? $techForm->number
                    : null;

                if ($i) {
                    $number .= '/' . ($i + 1);
                }

                if ($this->isSameTripTicket(
                    $medicForm ? $medicForm->uuid : null,
                    $techForm ? $techForm->uuid : null)
                ) {
                    continue;
                }

                $tripTicket = TripTicket::create([
                    'ticket_number' => $number,
                    'start_date' => $date,
                    'validity_period' => 1,
                    'medic_form_id' => $medicForm ? $medicForm->uuid : null,
                    'tech_form_id' => $techForm ? $techForm->uuid : null,
                    'logistics_method' => $action->getLogisticsMethod(),
                    'transportation_type' => $action->getTransportationType(),
                    'template_code' => $action->getTemplateCode(),
                ]);

                $tripTicketIds[] = $tripTicket->uuid;
            }
        }

        return $tripTicketIds;
    }

    private function getMedicForms(TripTicketsAction $action, Carbon $date): array
    {
        return $this->getFormBuilder($action, $date)
            ->select('uuid')
            ->join(
                'medic_forms',
                'medic_forms.forms_uuid',
                '=',
                'forms.uuid'
            )
            ->where('type_anketa', '=', FormTypeEnum::MEDIC)
            ->where('medic_forms.admitted', '=', 'Допущен')
            ->orderBy('medic_forms.type_view', 'desc')
            ->get()
            ->toArray();
    }

    private function getTechForms(TripTicketsAction $action, Carbon $date): array
    {
        return $this->getFormBuilder($action, $date)
            ->select([
                'uuid',
                'number_list_road as number',
            ])
            ->join(
                'tech_forms',
                'tech_forms.forms_uuid',
                '=',
                'forms.uuid'
            )
            ->where('type_anketa', '=', FormTypeEnum::TECH)
            ->where('tech_forms.point_reys_control', '=', 'Пройден')
            ->orderBy('tech_forms.type_view', 'desc')
            ->get()
            ->toArray();
    }

    private function getFormBuilder(TripTicketsAction $action, Carbon $date): Builder
    {
        return DB::table('forms')
            ->where('company_id', '=', $action->getCompanyId())
            ->where('driver_id', '=', $action->getDriverId())
            ->where(DB::raw('DATE(date)'), '=', $date)
            ->whereNull('deleted_id')
            ->whereNull('deleted_at');
    }

    private function isSameTripTicket($medicFormId, $techFormId): bool
    {
        $tripTicket = DB::table('trip_tickets')
            ->when($medicFormId, function ($query) use ($medicFormId) {
                $query->where('medic_form_id', '=', $medicFormId);
            })
            ->when($techFormId, function ($query) use ($techFormId) {
                $query->where('tech_form_id', '=', $techFormId);
            })
            ->first();

        return $tripTicket !== null;
    }
}
