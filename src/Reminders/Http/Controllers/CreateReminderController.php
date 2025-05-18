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

        $usersToNotify = $request->input('usersToNotify') ?? [];
        $usersToNotify = array_map(static function ($user) {
            return (int) $user['id'];
        }, $usersToNotify);

        $expiresInMinutes = $request->input('expiresInMinutes');
        if ($expiresInMinutes !== null) {
            $expiresInMinutes = (int) $expiresInMinutes;
        }

        try {
            $handler->handle(new CreateReminderCommand(
                $request->input('title'),
                $request->input('content'),
                ReminderAction::from($request->input('action.id')),
                $conditionBuilder->build($request->input('conditions', [])),
                ReminderStatus::from($request->input('status.id')),
                ReminderType::from($request->input('type.id')),
                filter_var($request->input('hiddenFromInitiator'), FILTER_VALIDATE_BOOLEAN),
                $usersToNotify,
                $request->input('expiresAt') ? \DateTimeImmutable::createFromFormat('Y-m-d H:i', $request->input('expiresAt')) : null,
                $expiresInMinutes
            ));

            DB::commit();

            return response()->noContent();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
