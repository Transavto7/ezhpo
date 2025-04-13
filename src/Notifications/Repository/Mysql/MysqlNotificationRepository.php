<?php

namespace Src\Notifications\Repository\Mysql;

use Illuminate\Support\Facades\DB;
use Src\Notifications\Entitites\Notification;
use Src\Notifications\Normalizer\NotificationDatabaseNormalizer;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsRepository;
use Src\Notifications\Queries\NotificationViewModel;
use Src\Notifications\Repository\NotificationRepository;

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
        $rawNotifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('viewed_at')
            ->whereNull('completed_at')
            ->get()
            ->toArray();

        return array_map(function ($rawNotification) {
            return NotificationViewModel::createFromNotification($this->normalizer->denormalize((array) $rawNotification));
        }, $rawNotifications);
    }

    public function countUnread(int $userId): int
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('viewed_at')
            ->whereNull('completed_at')
            ->count('notifications.id');
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
}
