<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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

        $user = Auth::user();

        $usersToNotify = $request->input('usersToNotify') ?? [];
        $usersToNotify = array_map(static function ($user) {
            return (int) $user['id'];
        }, $usersToNotify);

        $expiresInMinutes = $request->input('expiresInMinutes');
        if ($expiresInMinutes !== null) {
            $expiresInMinutes = (int) $expiresInMinutes;
        }

        try {
            $handler->handle(new UpdateReminderCommand(
                Uuid::fromString($reminderId),
                $request->input('title'),
                $request->input('content'),
                ReminderAction::from($request->input('action.id')),
                $conditionBuilder->build($request->input('conditions', [])),
                ReminderStatus::from($request->input('status.id')),
                ReminderType::from($request->input('type.id')),
                filter_var($request->input('hiddenFromInitiator'), FILTER_VALIDATE_BOOLEAN),
                $usersToNotify,
                $request->input('expiresAt') ? \DateTimeImmutable::createFromFormat('Y-m-d H:i', $request->input('expiresAt')) : null,
                $expiresInMinutes,
                $user->id
            ));

            DB::commit();

            return response()->noContent();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
