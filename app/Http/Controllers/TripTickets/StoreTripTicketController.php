<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketAction;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketActionItem;
use App\Actions\TripTicket\StoreTripTicket\StoreTripTicketHandler;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Http\Controllers\Controller;
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
                $items
            ));

            DB::commit();
        } catch (Throwable $exception) {
            $response['errors'] = [$exception->getMessage()];

            DB::rollBack();
        }

        return back()->with($response);
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
