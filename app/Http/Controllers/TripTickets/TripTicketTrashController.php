<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\DeleteTripTickets\TrashTripTicketHandler;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketTrashController extends Controller
{
    public function __invoke(Request $request, TrashTripTicketHandler $handler)
    {
        $uuid = $request->input('id');
        $action = $request->input('action');
        $tripTicket = TripTicket::withTrashed()->where('uuid', '=', $uuid)->first();

        try {
            DB::beginTransaction();

            $handler->handle($tripTicket, $action, Auth::user());

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            session()->flash('not_deleted_items', [$uuid]);
        }

        return redirect(url()->previous());
    }
}
