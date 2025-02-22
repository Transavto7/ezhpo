<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Actions\Anketa\CreateMedicFormHandler;
use App\Actions\TripTicket\TripTicketNumberGenerator;
use App\Enums\FormTypeEnum;
use App\Enums\TripTicket\TripTicketStatus;
use App\Enums\TripTicket\TripTicketType;
use App\Events\TripTickets\UpdateRelatedItems;
use App\Models\TripTicket;
use App\ValueObjects\EntityId;
use Carbon\Carbon;
use DateTimeImmutable;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

final class StoreTripTicketHandler extends TripTicketNumberGenerator
{
    /**
     * @var CreateMedicFormHandler
     */
    private $medicHandler;

    /**
     * @param CreateMedicFormHandler $medicHandler
     */
    public function __construct(CreateMedicFormHandler $medicHandler)
    {
        $this->medicHandler = $medicHandler;
    }

    /**
     * @throws Exception
     */
    public function handle(StoreTripTicketAction $action): array
    {
        $user = Auth::user();
        $tripTickets = [];

        DB::beginTransaction();

        foreach ($action->getItems() as $item) {
            $form = $action->getForm();
            $companyId = $action->getCompanyId();
            $driverId = $action->getDriverId();
            $carId = $action->getCarId();
            $startDate = $item->getStartDate();
            $periodPl = $item->getPeriodPl();

            if ($form) {
                $companyId = $form->company_id;
                if ($form->driver_id) {
                    $driverId = $form->driver_id;
                }
                if ($form->details->car_id) {
                    $carId = $form->details->car_id;
                }
                if ($form->date) {
                    $startDate = new DateTimeImmutable($form->date);
                }
                if ($form->details->period_pl) {
                    $periodPl = $form->details->period_pl;
                }
            }

            if ($item->getExternalTicketNumber() && $this->findSimilar($item->getExternalTicketNumber(), $companyId)) {
                DB::rollBack();
                throw new Exception("Путевой лист с номером {$item->getExternalTicketNumber()} уже существует");
            }

            if (!$startDate && $periodPl && !$this->checkPeriod($periodPl)) {
                DB::rollBack();
                throw new Exception("Неверный формат периода ПЛ {$periodPl}");
            }

            if ($startDate && $periodPl && $startDate->format('Y-m') !== $periodPl) {
                DB::rollBack();
                $period = Carbon::parse($periodPl);
                throw new Exception("Период ПЛ {$period->format('m.Y')} не совпадает с месяцем начала действия {$startDate->format('d.m.Y')}");
            }

            $id = EntityId::next()->getId();
            $medicFormId = null;
            $techFormId = null;
            $periodPl = $startDate
                ? $startDate->format('Y-m')
                : $periodPl;

            if ($action->isCreateIsDopMedic()) {
                $data = [
                    'type_anketa' => 'medic',
                    'is_dop' => '1',
                    'company_id' => $companyId,
                    'driver_id' => $driverId,
                    'anketa' => [
                        0 => [
                            'date' => $startDate ? $startDate->format('Y-m-d') : null,
                            'dates' => null,
                            'period_pl' => $periodPl,
                            'type_view' => 'Предрейсовый/Предсменный',
                        ]
                    ]
                ];

                $response = $this->medicHandler->handle($data, Auth::user());
                $medicFormId = $response['created'][0]->id;
            }

            if ($form) {
                $formId = $form->id;
                $type = $form->type_anketa;
                if ($type === FormTypeEnum::MEDIC) {
                    $medicFormId = $formId;
                } elseif ($type === FormTypeEnum::TECH) {
                    $techFormId = $formId;
                }
            }

            $tripTickets[] = TripTicket::create([
                'uuid' => $id,
                'ticket_number' => $this->getTicketNumber($id),
                'external_number' => $item->getExternalTicketNumber(),
                'company_id' => $companyId,
                'start_date' => $startDate,
                'period_pl' => $periodPl,
                'validity_period' => $item->getValidityPeriod(),
                'medic_form_id' => $medicFormId,
                'tech_form_id' => $techFormId,
                'driver_id' => $driverId,
                'car_id' => $carId,
                'logistics_method' => $item->getLogisticsMethod(),
                'transportation_type' => $item->getTransportationType(),
                'template_code' => $item->getTemplateCode(),
                'employee_id' => $user->relatedEmployee->id,
                'status' => TripTicketStatus::CREATED,
                'type' => $startDate
                    ? TripTicketType::COMMON
                    : TripTicketType::IN_ADVANCE,
            ]);

            if ($form) {
                event(new UpdateRelatedItems($tripTickets[0]));
            }
        }

        DB::commit();

        return $tripTickets;
    }

    private function findSimilar(string $number, string $companyId): bool
    {
        $similar = TripTicket::withTrashed()
            ->where(function (Builder $query) use ($number) {
                $query->where('ticket_number', '=', $number)
                    ->orWhere('external_number', '=', $number);
            })
            ->where('company_id', '=', $companyId)
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->first();

        return $similar !== null;
    }

    private function checkPeriod(string $periodPl): bool
    {
        if (preg_match('/^\d{4}-\d{2}$/', $periodPl) !== 1) {
            return false;
        }

        $date = Carbon::createFromFormat('!Y-m', $periodPl);
        return $date && $date->format('Y-m') === $periodPl;
    }
}
