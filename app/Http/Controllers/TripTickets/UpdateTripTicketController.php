<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\UpdateTripTicket\UpdateTripTicketAction;
use App\Actions\TripTicket\UpdateTripTicket\UpdateTripTicketHandler;
use App\Car;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Throwable;

class UpdateTripTicketController extends Controller
{
    public function __invoke(string $uuid, Request $request, UpdateTripTicketHandler $handler)
    {
        $referer = $request->input('REFERER');

        try {
            $tripTicket = TripTicket::where('uuid', '=', $uuid)->first();

            $this->validateIds(
                $request->input('driver_id'),
                $request->input('car_id')
            );

            $tripTicket = $handler->handle(new UpdateTripTicketAction(
                $tripTicket,
                $request->input('driver_id'),
                $request->input('car_id'),
                $request->input('start_date'),
                $request->input('validity_period', 1),
                LogisticsMethodEnum::fromString($request->input('logistics_method')),
                TransportationTypeEnum::fromString($request->input('transportation_type')),
                TripTicketTemplateEnum::fromString($request->input('template_code')),
            ));

        } catch (Throwable $exception) {
            return redirect($referer)->with(['error' => $exception->getMessage()]);
        }

        return redirect($referer)->with(['success' => "Путевой лист №$tripTicket->ticket_number обновлен"]);
    }

    private function validateIds(string $driverId = null, string $carId = null)
    {
        $driver = Driver::where('hash_id', '=', $driverId)->first();
        if ($driverId && $driver === null) {
            throw new NotFoundException("Ошибка. Водитель с hash_id $driverId не найден");
        }

        $car = Car::where('hash_id', '=', $carId)->first();
        if ($carId && $car === null) {
            throw new NotFoundException("Ошибка. Автомобиль с hash_id $carId не найден");
        }
    }
}
