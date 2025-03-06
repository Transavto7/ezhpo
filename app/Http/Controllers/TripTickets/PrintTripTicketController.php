<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Services\TripTicketExporter\TripTicketExporter;
use App\ValueObjects\EntityId;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PrintTripTicketController extends Controller
{
    public function __invoke(Request $request, TripTicketExporter $exporter)
    {
        try {
            $id = EntityId::fromString($request->input('id'));

            $writer = $exporter->export($id);

            $fileName = $exporter->getExportFileName($id);

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
