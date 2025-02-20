<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use App\Services\TripTicket\TripTicketPermissions;
use App\Services\TripTicketExporter\TripTicketExporter;
use App\ValueObjects\EntityId;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PrintTripTicketController extends Controller
{
    public function __invoke(Request $request, TripTicketExporter $exporter)
    {
        try {
            $files = Storage::disk('public')->files('qrcodes');

            foreach ($files as $file) {
                if (basename($file) !== '.gitignore') {
                    Storage::disk('public')->delete($file);
                }
            }

            $tripTicket = TripTicket::where('uuid', '=', $request->input('id'))->firstOrFail();
            $id = EntityId::fromString($tripTicket->uuid);

            if (! TripTicketPermissions::canPrint($tripTicket->type, $tripTicket->status, $tripTicket->medic_form_id)) {
                throw new Exception("Путевой лист № $tripTicket->ticket_number не может быть напечатан");
            }

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
