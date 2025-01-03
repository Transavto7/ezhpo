<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Services\TripTicketExporter\TripTicketExporter;
use App\ValueObjects\EntityId;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExportTripTicketController extends Controller
{
    public function __invoke(Request $request, TripTicketExporter $exporter)
    {
        try {
            return $exporter->export(EntityId::fromString($request->input('id')));
        } catch (Exception $e) {
            return response()
                ->json(['error' => $e->getMessage(),])
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
