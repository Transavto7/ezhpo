<?php

namespace App\Http\Controllers\TripTickets;

use App\Enums\FormTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TripTicketCreateFormPageController extends Controller
{
    public function __invoke(string $id, string $type)
    {
        $tripTicket = TripTicket::where('uuid', '=', $id)->first();
        if (!$tripTicket) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $formTypeLabels = [
            FormTypeEnum::TECH => 'ТО',
            FormTypeEnum::MEDIC => 'МО'
        ];

        $view = "trip-tickets.create-$type-form";

        if (!isset($formTypeLabels[$type]) || !view()->exists($view)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Тип осмотра - $type не может быть добавлен к ПЛ");
        }

        date_default_timezone_set('UTC');
        $time = time();
        $user = Auth::user();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d', $time);

        $formPointIdSessionKey = 'anketa_pv_id';
        if (Session::exists($formPointIdSessionKey) && ((date('d.m') > Session::get($formPointIdSessionKey)['expired']))) {
            Session::remove($formPointIdSessionKey);
        }

        $previousUrl = url()->previous();
        if ($previousUrl) {
            $previousRequest = Request::create($previousUrl);
            if ($previousRequest->is("trip-tickets*")) {
                $queryParams = $previousRequest->except(['orderKey', 'orderBy']);
                if (count($queryParams) !== 0) {
                    Session::put('trip-tickets.index-page.filters', $queryParams);
                }
            }
        }

        return view($view, [
            'tripTicket' => $tripTicket,
            'title' => "Добавление $formTypeLabels[$type] по данным путевого листа $tripTicket->ticket_number",
            'default_current_date' => $time,
            'default_pv_id' => $user->pv_id,
        ]);
    }
}
