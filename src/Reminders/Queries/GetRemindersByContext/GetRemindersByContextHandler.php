<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

final class GetRemindersByContextHandler
{
    /** @var GetReminderByContextRepository */
    private $repository;

    /**
     * @param GetReminderByContextRepository $repository
     */
    public function __construct(GetReminderByContextRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetRemindersByContextQuery $query
     * @return ReminderByContextViewModel[]
     */
    public function handle(GetRemindersByContextQuery $query): array
    {
        return $this->repository->getReminderByContext($query->getAction(), $query->getContext());
    }
}
