<?php

namespace Src\Notifications\Repositories\Mysql;

use Illuminate\Support\Facades\DB;
use Src\Notifications\Entities\NotificationLog;
use Src\Notifications\Normalizer\NotificationLogDatabaseNormalizer;
use Src\Notifications\Repositories\NotificationLogRepository;

class MysqlNotificationLogRepository implements NotificationLogRepository
{
    /** @var NotificationLogDatabaseNormalizer */
    private $normalizer;

    /**
     * @param NotificationLogDatabaseNormalizer $normalizer
     */
    public function __construct(NotificationLogDatabaseNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function add(NotificationLog $notificationLog)
    {
        DB::table('notification_logs')->insert($this->normalizer->normalize($notificationLog));
    }
}
