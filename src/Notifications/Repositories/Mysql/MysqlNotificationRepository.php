<?php

namespace Src\Notifications\Repositories\Mysql;

use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entities\Notification;
use Src\Notifications\Normalizer\NotificationDatabaseNormalizer;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsRepository;
use Src\Notifications\Queries\GetUnreadUserNotifications\NotificationViewModel;
use Src\Notifications\Repositories\NotificationRepository;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderType;

class MysqlNotificationRepository implements NotificationRepository, GetUnreadUserNotificationsRepository
{
    /** @var NotificationDatabaseNormalizer */
    private $normalizer;

    /**
     * @param NotificationDatabaseNormalizer $normalizer
     */
    public function __construct(NotificationDatabaseNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function add(Notification $notification)
    {
        DB::table('notifications')->insert($this->normalizer->normalize($notification));
    }

    public function getUnread(int $userId): array
    {
        $items = DB::table('notifications as n')
            ->select([
                'n.*',
                'r.type as reminder_type',
                'r.action as reminder_action',
            ])
            ->leftJoin('reminders as r', 'r.id', '=', 'n.reminder_id')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->whereNull('completed_at')
            ->orderByRaw('n.user_id = n.initiator_user_id desc')
            ->orderByRaw('read_at is not null')
            ->orderByDesc('created_at')
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return NotificationViewModel::createFrom(
                $this->normalizer->denormalize((array) $item),
                ReminderType::from($item->reminder_type),
                ReminderAction::from($item->reminder_action),
            );
        }, $items);
    }

    public function update(Notification $notification)
    {
        DB::table('notifications')
            ->where('id', $notification->getId()->value())
            ->update($this->normalizer->normalize($notification));
    }

    /**
     * @throws \Exception
     */
    public function findById(string $id): ?Notification
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->first();

        if ($notification === null) {
            return null;
        }

        return $this->normalizer->denormalize((array) $notification);
    }

    public function getReminderIdsWithCompletedNotifications(array $reminderIds): array
    {
        $items = DB::table('notifications')
            ->select(['reminder_id'])
            ->whereIn('reminder_id', $reminderIds)
            ->whereNotNull('completed_at')
            ->distinct()
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return Uuid::fromString($item->reminder_id);
        }, $items);
    }

    public function getReminderIdsWithViewedNotificationsByUser(array $reminderIds, int $userId): array
    {
        $items = DB::table('notifications')
            ->select(['reminder_id'])
            ->whereIn('reminder_id', $reminderIds)
            ->where('user_id', $userId)
            ->whereNotNull('read_at')
            ->distinct()
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return Uuid::fromString($item->reminder_id);
        }, $items);
    }
}
