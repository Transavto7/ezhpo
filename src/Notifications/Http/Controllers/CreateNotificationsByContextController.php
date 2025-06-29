<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\CreateNotificationsByContext\ContextBuilder;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextCommand;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderSubjectType;
use Symfony\Component\HttpFoundation\Response;

final class CreateNotificationsByContextController
{
    public function __invoke(
        Request $request,
        Dispatcher $dispatcher
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        $action = ReminderAction::from($request->input('action'));

        $rawContext = $request->input('context') ?? [];

        $dispatcher->dispatch(new CreateNotificationsByContextCommand(
            $action,
            $user,
            ContextBuilder::create()
                ->city($rawContext['city'] ?? null)
                ->company($rawContext['company'] ?? null)
                ->point($rawContext['point'] ?? null)
                ->roles($rawContext['roles'] ?? [])
                ->role($rawContext['role'] ?? null)
                ->subject($rawContext['subject'] ?? null)
                ->subjectType($rawContext['subject_type'] ? ReminderSubjectType::from($rawContext['subject_type']) : null),
        ));

        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
