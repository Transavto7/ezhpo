<?php

namespace App\Actions\TripTicket\CreateTripTickets;

use App\Actions\TripTicket\TripTicketNumberGenerator;
use App\Enums\FormTypeEnum;
use App\Models\TripTicket;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Query\Builder;

final class TripTicketsHandler extends TripTicketNumberGenerator
{
    /**
     * @throws Exception
     */
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

                if ($techForm !== null) {
                    $carId = $techForm->car_id;
                }

                $tripTicketId = $this->existedTripTicket(
                    $medicForm ? $medicForm->id : null,
                    $techForm ? $techForm->id : null);

                if ($tripTicketId !== null) {
                    continue;
                }

                $tripTicket = TripTicket::create([
                    'ticket_number' => $this->nextTicketNumber($action->getCompany()->hash_id),
                    'company_id' => $action->getCompany()->hash_id,
                    'start_date' => $date->copy(),
                    'validity_period' => $action->getValidityPeriod(),
                    'medic_form_id' => $medicForm ? $medicForm->id : null,
                    'driver_id' => $action->getDriver() ? $action->getDriver()->hash_id : null,
                    'tech_form_id' => $techForm ? $techForm->id : null,
                    'car_id' => $carId,
                    'logistics_method' => $action->getLogisticsMethod(),
                    'transportation_type' => $action->getTransportationType(),
                    'template_code' => $action->getTemplateCode(),
                ]);

                $tripTicketIds[] = $tripTicket;
            }
        }

        return $tripTicketIds;
    }

    private function getMedicForms(TripTicketsAction $action, Carbon $date): array
    {
        return $this->getFormBuilder($action, $date)
            ->select('id')
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
                'id',
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
            ? $tripTicket->id
            : null;
    }
}
