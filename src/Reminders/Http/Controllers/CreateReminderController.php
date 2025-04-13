<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Src\Reminders\Commands\CreateReminder\CreateReminderCommand;
use Src\Reminders\Commands\CreateReminder\CreateReminderHandler;
use Src\Reminders\ConditionBuilder\SelectsArrayConditionBuilder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;
use Src\Reminders\Http\Requests\CreateReminderRequest;
use Throwable;

final class CreateReminderController
{
    public function __invoke(
        CreateReminderRequest $request,
        SelectsArrayConditionBuilder $conditionBuilder,
        CreateReminderHandler $handler
    ): Response {
        DB::beginTransaction();
        try {
            $handler->handle(new CreateReminderCommand(
                $request->input('title'),
                $request->input('content'),
                ReminderAction::from($request->input('action.id')),
                $conditionBuilder->build($request->input('conditions', [])),
                ReminderStatus::enable(),
                ReminderType::from('info'),
                filter_var($request->input('hidden_from_initiator'), FILTER_VALIDATE_BOOLEAN),
                $request->input('users_to_notify') ?? [],
                $request->input('expires_at') ? \DateTimeImmutable::createFromFormat('Y-m-d H:i', $request->input('expires_at')) : null,
                $request->input('expires_in_minutes')
            ));

            DB::commit();

            return response()->noContent();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
