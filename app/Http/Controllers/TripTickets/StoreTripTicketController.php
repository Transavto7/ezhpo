<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketAction;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketActionItem;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketHandler;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\TripTicket\LogisticsMethodEnum;
use App\Enums\TripTicket\TransportationTypeEnum;
use App\Enums\TripTicket\TripTicketTemplateEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Services\TripTicket\TripTicketPermissions;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class StoreTripTicketController extends Controller
{
    public function __invoke(Request $request, StoreTripTicketHandler $handler)
    {
        $response = [];
        try {
            $createIsDopMedic = $request->has('create_is_dop_medic');
            $form = null;
            if ($request->query('form_id')) {
                $form = Form::find($request->query('form_id'));

                if ($form && ($form->tripTicketTech || $form->tripTicketMedic)) {
                    throw new \DomainException("Осмотр $form->id уже связан с путевым листом");
                }
            }

            $this->validateIds(
                $request->input('company_id'),
                $request->input('driver_id'),
                $request->input('car_id')
            );

            $items = $this->getItems($request->input('trip_ticket'));

            DB::beginTransaction();
            $response['created'] = $handler->handle(new StoreTripTicketAction(
                $request->input('company_id'),
                $request->input('driver_id'),
                $request->input('car_id'),
                $items,
                $createIsDopMedic,
                $form
            ));

            DB::commit();
        } catch (Throwable $exception) {
            $response['errors'] = [$exception->getMessage()];

            DB::rollBack();
        }

        $response['can_print'] = false;

        if (isset($response['created'])) {
            foreach ($response['created'] as $tripTicket) {
                if (TripTicketPermissions::canPrint($tripTicket->type, $tripTicket->status, $tripTicket->medic_form_id)) {
                    $response['can_print'] = true;
                }
            }
        }

        return redirect(route('trip-tickets.create'))->with($response);
    }

    private function validateIds(string $companyId, string $driverId = null, string $carId = null)
    {
        $company = Company::where('hash_id', '=', $companyId)->first();
        if ($companyId && $company === null) {
            throw new NotFoundException("Ошибка. Компания с hash_id $companyId не найдена");
        }

        $driver = Driver::where('hash_id', '=', $driverId)->first();
        if ($driverId && $driver === null) {
            throw new NotFoundException("Ошибка. Водитель с hash_id $driverId не найден");
        }

        $car = Car::where('hash_id', '=', $carId)->first();
        if ($carId && $car === null) {
            throw new NotFoundException("Ошибка. Автомобиль с hash_id $carId не найден");
        }
    }

    private function getItems(array $data)
    {
        return array_map(function (array $item) {
            return new StoreTripTicketActionItem(
                $item['date_from']
                    ? new \DateTimeImmutable($item['date_from'])
                    : null,
                $item['period_pl'],
                $item['validity_period'] ?: 1,
                $item['ticket_number'],
                LogisticsMethodEnum::fromString($item['logistics_method']),
                TransportationTypeEnum::fromString($item['transportation_type']),
                TripTicketTemplateEnum::fromString($item['template_code'])
            );
        }, $data);
    }
}
