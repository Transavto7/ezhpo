<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Commands\UpdateReminder\UpdateReminderCommand;
use Src\Reminders\Commands\UpdateReminder\UpdateReminderHandler;
use Src\Reminders\ConditionBuilder\SelectsArrayConditionBuilder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;
use Src\Reminders\Http\Requests\UpdateReminderRequest;
use Throwable;

final class UpdateReminderController
{
    public function __invoke(
        string $reminderId,
        UpdateReminderRequest $request,
        UpdateReminderHandler $handler,
        SelectsArrayConditionBuilder $conditionBuilder
    ) {
        DB::beginTransaction();
        try {
            $handler->handle(new UpdateReminderCommand(
                Uuid::fromString($reminderId),
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
