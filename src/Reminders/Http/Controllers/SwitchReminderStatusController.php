<?php

namespace Src\Reminders\Http\Controllers;

use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Commands\SwitchReminderStatus\SwitchReminderStatusCommand;
use Src\Reminders\Enums\ReminderStatus;
use Symfony\Component\HttpFoundation\Response;

final class SwitchReminderStatusController
{
    public function __invoke(string $id, Request $request, Dispatcher $dispatcher)
    {
        $user = Auth::user();

        try {
            $status = $request->input('enable') ? ReminderStatus::enable() : ReminderStatus::disable();

            $dispatcher->dispatch(new SwitchReminderStatusCommand(Uuid::fromString($id), $user->id, $status));

            return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
