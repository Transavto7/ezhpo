<?php

namespace App\Actions\TripTicket\CreateTripTickets;

use App\Enums\FormTypeEnum;
use App\Models\TripTicket;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;

final class TripTicketsHandler
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
                $carId = null;
                $number = null;

                if ($techForm !== null) {
                    $carId = $techForm->car_id;
                    $number = $techForm->number;
                }

                if ($number === null) {
                    $number = $action->getCompany()->hash_id
                        .'-'
                        .date('d.m.Y', strtotime($date));
                }

                if ($i) {
                    $number .= '/' . ($i + 1);
                }

                $tripTicketId = $this->existedTripTicket(
                    $medicForm ? $medicForm->uuid : null,
                    $techForm ? $techForm->uuid : null);

                if ($tripTicketId !== null) {
                    $tripTicketIds[] = $tripTicketId;
                    continue;
                }

                $tripTicket = TripTicket::create([
                    'ticket_number' => $number,
                    'company_id' => $action->getCompany()->hash_id,
                    'start_date' => $date,
                    'validity_period' => $action->getValidityPeriod(),
                    'medic_form_id' => $medicForm ? $medicForm->uuid : null,
                    'driver_id' => $action->getDriver() ? $action->getDriver()->hash_id : null,
                    'tech_form_id' => $techForm ? $techForm->uuid : null,
                    'car_id' => $carId,
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
                'car_id',
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
            ->where('company_id', '=', $action->getCompany()->hash_id)
            ->where('driver_id', '=', $action->getDriver() ? $action->getDriver()->hash_id : null)
            ->where(DB::raw('DATE(date)'), '=', $date)
            ->whereNull('deleted_id')
            ->whereNull('deleted_at');
    }

    private function existedTripTicket($medicFormId, $techFormId)
    {
        $tripTicket = DB::table('trip_tickets')
            ->when($medicFormId, function ($query) use ($medicFormId) {
                $query->where('medic_form_id', '=', $medicFormId);
            })
            ->when($techFormId, function ($query) use ($techFormId) {
                $query->where('tech_form_id', '=', $techFormId);
            })
            ->first();

        return $tripTicket !== null
            ? $tripTicket->uuid
            : null;
    }
}
