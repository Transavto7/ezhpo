<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Commands\ShowReminderLogs\ShowReminderLogsCommand;
use Src\Reminders\Commands\ShowReminderLogs\ShowReminderLogsHandler;
use Src\Reminders\Queries\GetRemindersTableItems\RemindersTableFilters;

final class JournalReminderLogsController
{
    public function __invoke(Request $request, ShowReminderLogsHandler $handler): JsonResponse
    {
        $sortOrder = null;
        if ($request->input('sortDesc') !== null) {
            $sortOrder = filter_var($request->input('sortDesc'), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        }

        $reminderFilterRaw = $request->input('filters');
        $isUseReminderFilter = false;
        foreach ($reminderFilterRaw as $field => $value) {
            if (!empty($value)) {
                $isUseReminderFilter = true;
                break;
            }
        }
        $reminderFilter = $isUseReminderFilter
            ? new RemindersTableFilters(
                $request->input('filters.search'),
                $request->input('filters.cities'),
                $request->input('filters.companies'),
                $request->input('filters.points'),
                $request->input('filters.roles'),
                $request->input('filters.subjects'),
                $request->input('filters.subject_type'),
                $request->input('filters.users'),
                $request->input('filters.actions'),
            )
            : null;

        $command = new ShowReminderLogsCommand(
            $reminderFilter,
            (int)$request->input('page'),
            (int)$request->input('perPage'),
            $request->input('sortBy'),
            $sortOrder,
        );

        return response()->json($handler->handle($command));
    }
}
