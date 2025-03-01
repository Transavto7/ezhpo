<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\DeleteTripTickets\TrashTripTicketHandler;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripTicketMassTrashController extends Controller
{
    public function __invoke(Request $request, TrashTripTicketHandler $handler): JsonResponse
    {
        $ids = $request->input('ids') ?? [];
        $action = $request->input('action');
        $notDeleted = [];

        foreach ($ids as $uuid) {
            try {
                DB::beginTransaction();

                $tripTicket = TripTicket::withTrashed()->where('uuid', '=', $uuid)->first();
                $handler->handle($tripTicket, $action, Auth::user());

                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();

                $notDeleted[] = $uuid;
            }
        }

        if (count($notDeleted)) {
            session()->flash('not_deleted_items', $notDeleted);
        }

        return response()->json();
    }
}
