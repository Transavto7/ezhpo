<?php
declare(strict_types=1);

namespace Src\Reminders\Queries\GetReminderById;

use Src\Reminders\Repositories\RemindersRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetReminderByIdHandler
{
    /** @var GetReminderRepositoryInterface */
    private $repository;

    /**
     * @param GetReminderRepositoryInterface $repository
     */
    public function __construct(GetReminderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(GetReminderByIdQuery $query): ReminderViewModel
    {
        $viewModel = $this->repository->getReminderViewModelById($query->getReminderId());

        if ($viewModel === null) {
            throw new NotFoundHttpException('Reminder not found');
        }

        return $viewModel;
    }
}
