<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use App\Services\TripTicket\TripTicketPermissions;
use App\Services\TripTicketExporter\TripTicketExporter;
use App\ValueObjects\EntityId;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MassPrintTripTicketsController extends Controller
{
    public function __invoke(Request $request, TripTicketExporter $exporter)
    {
        try {
            $ids = array_map(function (string $id) {
                return EntityId::fromString($id);
            }, $request->input('ids'));

            if (count($ids) >= 15) {
                throw new DomainException('Количество ПЛ для печати не должно превышать 15');
            }

            $tripTickets = TripTicket::whereIn('uuid', $ids)->get();

            foreach ($tripTickets as $tripTicket) {
                if (! TripTicketPermissions::canPrint($tripTicket->type, $tripTicket->status, $tripTicket->medic_form_id)) {
                    throw new Exception("Путевой лист № $tripTicket->ticket_number не может быть напечатан");
                }
            }

            $writer = $exporter->massExport($ids);

            $fileName = $exporter->getMassExportFileName($ids);

            $response = new StreamedResponse(
                function () use ($writer) {
                    $writer->save('php://output');
                }
            );

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set(
                'Content-Disposition',
                "attachment;filename=\"$fileName\""
            );
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } catch (Exception $e) {
            return response()
                ->json(['error' => $e->getMessage(),])
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
