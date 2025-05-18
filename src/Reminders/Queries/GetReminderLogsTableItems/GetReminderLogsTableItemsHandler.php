<?php

namespace Src\Reminders\Queries\GetReminderLogsTableItems;

use Illuminate\Support\Facades\DB;
use Src\Core\EntityMap;
use Src\Reminders\ConditionBuilder\AvailableConditions;
use Src\Reminders\Enums\ReminderLogAction;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\UsersFilter;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsHandler;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsQuery;

class GetReminderLogsTableItemsHandler
{
    private $reminderHandler;

    public function __construct(GetRemindersTableItemsHandler $reminderHandler)
    {
        $this->reminderHandler = $reminderHandler;
    }

    public function handle(GetReminderLogsTableItemsCommand $command): array
    {
        $query = DB::table('reminder_logs')
            ->select([
                'reminder_logs.action',
                'reminder_logs.payload',
                'reminder_logs.created_at',
                'reminders.id as reminder_id',
                'reminders.title',
                'reminder_logs.user_id',
                'employees.id as user_id',
                'employees.name as user_name',
            ])
            ->join('reminders', 'reminder_logs.reminder_id', '=', 'reminders.id')
            ->join('employees', 'reminder_logs.user_id', '=', 'employees.related_user_id');

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
            if (! empty($arrayFilter[UsersFilter::NAME])) {
                $query->orWhereIn('reminder_logs.user_id', $arrayFilter[UsersFilter::NAME]);
            }
        }

        $paginator = $query->paginate($command->getPerPage(), ['*'], 'page', $command->getPage());

        $entityMapList = [];

        $items = $paginator
            ->getCollection()->map(static function ($row) use (&$entityMapList) {
                $action = ReminderLogAction::from($row->action);
                $payload = json_decode($row->payload, true);

                foreach ($payload as $key => $value) {
                    if (empty(AvailableConditions::AVAILABLE_CONDITIONS[$key])) {
                        continue;
                    }
                    if (empty($entityMapList[$key])) {
                        $tableName = AvailableConditions::AVAILABLE_CONDITIONS[$key]::TABLE_NAME;
                        if (empty($tableName)) {
                            continue;
                        }
                        $entityMapList[$key] = (new EntityMap($tableName))
                            ->setNameField(AvailableConditions::AVAILABLE_CONDITIONS[$key]::FIELD_NAME);
                    }
                    /** @var EntityMap $entityMap */
                    $entityMap = $entityMapList[$key];
                    if (! empty($value['new'])) {
                        $entityMap->addId($value['new']);
                    }
                    if (! empty($value['old'])) {
                        $entityMap->addId($value['old']);
                    }
                }

                return [
                    'reminder_url' => route('reminders.list-page', ['id' => $row->reminder_id, 'title' => $row->title]),
                    'action' => [
                        'id' => $action->value(),
                        'name' => $action->getTitle(),
                    ],
                    'payload' => $payload,
                    'created_at' => $row->created_at,
                    'title' => $row->title,
                    'user' => $row->user_name,
                    'user_url' => route('employees.index', ['employee_id' => [$row->user_id]]),
                ];
            });

        $mapList = [];
        /** @var EntityMap $entityMap */
        foreach ($entityMapList as $key => $entityMap) {
            $mapList[$key] = $entityMap->getKeyValueMap();
        }

        return [
            'items' => $items->toArray(),
            'total' => $paginator->total(),
            'mapList' => $mapList,
        ];
//        return $tableItems->toArray();
    }
}
