<?php

namespace Src\Reminders\Commands\ShowReminderLogs;

use Illuminate\Support\Facades\DB;
use Src\Reminders\Enums\ReminderLogAction;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\UsersFilter;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsHandler;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsQuery;

class ShowReminderLogsHandler
{
    private $reminderHandler;
    public function __construct(GetRemindersTableItemsHandler $reminderHandler)
    {
        $this->reminderHandler = $reminderHandler;
    }

    public function handle(ShowReminderLogsCommand $command): array
    {

        $query = DB::table('reminder_logs')
            ->select([
                'reminder_logs.action',
                'reminder_logs.payload',
                'reminder_logs.created_at',
                'reminders.title',
                'reminder_logs.user_id',
                'users.name as user_name',
            ])
            ->join('reminders', 'reminder_logs.reminder_id', '=', 'reminders.id')
            ->join('users', 'reminder_logs.user_id', '=', 'users.id');

        if ($command->getSortBy() && $command->getOrderBy()) {
            $query->orderBy($command->getSortBy(), $command->getOrderBy());
        }

        if ($remindFilter = $command->getReminderFilters()) {
            $tableItems = $this->reminderHandler->handle(new GetRemindersTableItemsQuery(
                1,
                1000,
                null,
                null,
                $remindFilter,
            ));
            $remindIds = array_column($tableItems->getItems(), 'id');
            $query->whereIn('reminder_logs.reminder_id', $remindIds);

            $arrayFilter = $remindFilter->toArray();
            if (!empty($arrayFilter[UsersFilter::NAME])) {
                $query->orWhereIn('reminder_logs.user_id', $arrayFilter[UsersFilter::NAME]);
            }
        }

        $paginator = $query->paginate($command->getPerPage(), ['*'], 'page', $command->getPage());

        $items = $paginator
            ->getCollection()->map(static function ($row) {
                $action = ReminderLogAction::from($row->action);

                return [
                    'action' => [
                        'id' => $action->value(),
                        'name' => $action->toTranslate(),
                    ],
                    'payload' => $row->payload,
                    'created_at' => $row->created_at,
                    'title' => $row->title,
                    'user' => [
                        'id' => $row->user_id,
                        'name' => $row->user_name,
                    ]
                ];
            });

        return [
            'items' => $items->toArray(),
            'total' => $paginator->total(),
        ];
//        return $tableItems->toArray();
    }
}
