<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Actions\TripTicket\UpdateTripTicketForm\UpdateTripTicketFormAction;
use App\Actions\TripTicket\UpdateTripTicketForm\UpdateTripTicketFormHandler;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketStoreTechFormController extends Controller
{
    public function __invoke(string  $id, Request $request, CreateFormHandlerFactory $factory, UpdateTripTicketFormHandler $ticketHandler): RedirectResponse
    {
        $tripTicket = TripTicket::where('uuid', '=', $id)->first();
        $prevUrl = $request->input('REFERER');
        $data = $request->all();
        $data['type_anketa'] = FormTypeEnum::TECH;
        $data['company_id'] = $tripTicket->company_id;
        $data['driver_id'] = $tripTicket->driver_id ?: $request->input('driver_id');
        $data['anketa'][0]['car_id'] = $tripTicket->car_id ?: $request->input('car_id');
        $data['anketa'][0]['number_list_road'] = $tripTicket->ticket_number;

        try {
            $this->checkDriver($tripTicket->company_id, $request->input('driver_id'));

            session(['anketa_pv_id' => [
                'value' => $request->get('pv_id', 0),
                'expired' => date('d.m')
            ]]);

            $handler = $factory->make($data['type_anketa']);

            $responseData = $handler->handle($data, Auth::user());
            if (array_key_exists('created', $responseData) && count($responseData['created']) === 1) {
                $responseData['success'] = "Технический осмотр для ПЛ № $tripTicket->ticket_number успешно добавлен";

                $ticketHandler->handle(new UpdateTripTicketFormAction(
                    $tripTicket,
                    new \DateTimeImmutable($data['anketa'][0]['date']),
                    $data['type_anketa'],
                    $responseData['created'][0],
                    $request->input('driver_id'),
                    array_key_exists('car_id', $data['anketa'][0]) ? $data['anketa'][0]['car_id'] : null,
                ));
            }

            DB::commit();
        } catch (Throwable $exception) {
            $responseData['error'] = $exception->getMessage();

            DB::rollBack();
        }

        return redirect($prevUrl)->with($responseData);
    }

    private function checkDriver(string $companyId, $driverId)
    {
        if (! $driverId) {
            return;
        }

        $driver = Driver::where('hash_id',  '=', $driverId)->first();

        if ($driver) {
            if ($driver->company->hash_id !== $companyId) {
                throw new \Exception('Компания водителя не соответствует компании ПЛ');
            }
        } else {
            throw new \Exception("Водитель с ID $driverId не найден");
        }
    }
}
