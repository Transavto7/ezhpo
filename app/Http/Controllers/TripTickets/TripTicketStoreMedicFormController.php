<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Actions\TripTicket\UpdateTripTicketForm\UpdateTripTicketFormAction;
use App\Actions\TripTicket\UpdateTripTicketForm\UpdateTripTicketFormHandler;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketStoreMedicFormController extends Controller
{
    public function __invoke(string  $id, Request $request, CreateFormHandlerFactory $factory, UpdateTripTicketFormHandler $ticketHandler): RedirectResponse
    {
        $tripTicket = TripTicket::where('uuid', '=', $id)->first();
        $prevUrl = $request->input('REFERER');
        $data = $request->all();
        $data['type_anketa'] = FormTypeEnum::MEDIC;
        $data['company_id'] = $tripTicket->company_id;
        $data['driver_id'] = $tripTicket->driver_id;

        try {
            session(['anketa_pv_id' => [
                'value' => $request->get('pv_id', 0),
                'expired' => date('d.m')
            ]]);

            $handler = $factory->make($data['type_anketa']);

            $responseData = $handler->handle($data, Auth::user());
            if (array_key_exists('created', $responseData) && count($responseData['created']) === 1) {
                $responseData['success'] = "Медицинский осмотр для ПЛ № $tripTicket->ticket_number успешно добавлен";

                $ticketHandler->handle(new UpdateTripTicketFormAction(
                    $tripTicket,
                    $data['type_anketa'],
                    $responseData['created'][0]
                ));
            }

            DB::commit();
        } catch (Throwable $exception) {
            $responseData['error'] = $exception->getMessage();

            DB::rollBack();
        }

        return redirect($prevUrl)->with($responseData);
    }
}
