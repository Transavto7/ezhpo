<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Actions\TripTicket\SyncTripTicketWithForm\SyncTripTicketWithFormAction;
use App\Actions\TripTicket\SyncTripTicketWithForm\SyncTripTicketWithFormHandler;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketStoreFormController extends Controller
{
    public function __invoke(string $id, Request $request, CreateFormHandlerFactory $factory, SyncTripTicketWithFormHandler $ticketHandler): RedirectResponse
    {
        $tripTicket = TripTicket::where('uuid', '=', $id)->first();
        $data = $request->all();

        try {
            session(['anketa_pv_id' => [
                'value' => $request->get('pv_id', 0),
                'expired' => date('d.m')
            ]]);

            $handler = $factory->make($data['type_anketa']);

            $formTypeLabels = [
                FormTypeEnum::TECH => 'ТО',
                FormTypeEnum::MEDIC => 'МО'
            ];

            $formTypeLabel = $formTypeLabels[$data['type_anketa'] ?? ''] ?? 'Осмотр';

            $responseData = $handler->handle($data, Auth::user());
            if (!array_key_exists('created', $responseData) || count($responseData['created']) === 0) {
                throw new \Exception('Ошибка создания осмотра');
            }

            $responseData['success'] = "$formTypeLabel для ПЛ № $tripTicket->ticket_number успешно добавлен";

            $ticketHandler->handle(new SyncTripTicketWithFormAction(
                $tripTicket,
                $responseData['created'][0]
            ));

            DB::commit();

            return redirect(route('trip-tickets.index'))->with($responseData);
        } catch (Throwable $exception) {
            $responseData['errors'][] = $exception->getMessage();

            DB::rollBack();

            return back()->with(['errors' => $responseData['errors']]);
        }
    }
}
