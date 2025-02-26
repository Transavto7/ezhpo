<?php

namespace App\Actions\TripTicket\UpdateTripTicketPhotos;

use App\Enums\TripTicketActionType;
use App\Enums\TripTicketStatus;
use App\Events\TripTickets\ChangeTripTicketStatus;
use App\Events\TripTickets\LogTripTicket;
use Auth;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Storage;

final class UpdateTripTicketPhotosHandler
{
    public function handle(UpdateTripTicketPhotosAction $action)
    {
        $files = $this->storeFiles($action->getPhotos());

        $action->getTripTicket()
            ->fill([
                'photos' => array_merge($files, $action->getTripTicket()->photos ?: [])
            ]);

        if ($action->getTripTicket()->status !== TripTicketStatus::ACTIVATED) {
            event(new ChangeTripTicketStatus($action->getTripTicket(), TripTicketStatus::activated()));
            event(new LogTripTicket(Auth::user(), $action->getTripTicket(), TripTicketActionType::changeStatus()));
        }

        $action->getTripTicket()->save();
    }

    private function removeFiles(array $files)
    {
        foreach ($files as $file) {
            Storage::disk('public')->delete($file['path']);
        }
    }

    private function storeFiles(array $files): array
    {
        $processedFiles = [];

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $uuid = Uuid::uuid4()->toString();
            $extension = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();
            $path = Storage::disk('public')->putFileAs('trip-tickets', $file, $uuid.'.'.$extension);

            $processedFiles[] = [
                'original_name' => $filename,
                'path' => $path,
            ];
        }

        return $processedFiles;
    }
}
