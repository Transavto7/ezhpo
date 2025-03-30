<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Commands\DeleteReminder\DeleteReminderCommand;
use Src\Reminders\Commands\DeleteReminder\DeleteReminderHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteReminderController
{
    public function __invoke(Request $request, DeleteReminderHandler $handler)
    {
        DB::beginTransaction();
        try {
            $handler->handle(new DeleteReminderCommand(Uuid::fromString($request->get('reminder_id'))));
            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
