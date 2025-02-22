<?php

namespace Src\Users\Management\Queries\GetUsersListPage;

use App\Enums\UserEntityType;
use Src\Users\Management\Enums\UserStatusEnum;

final class GetUsersListPageHandler
{
    public function handle(GetUsersListPageQuery $params): UsersListPage
    {
        $statusFilterOptions = [
            new FilterOption(UserStatusEnum::UNBLOCKED, 'Нет'),
            new FilterOption(UserStatusEnum::BLOCKED, 'Да'),
        ];

        $entityTypeFilterOptions = [];
        $entityTypes = [UserEntityType::employee(), UserEntityType::terminal(), UserEntityType::company(), UserEntityType::driver()];
        foreach ($entityTypes as $entityType) {
            /**
             * @var UserEntityType $entityType
             */
            $entityTypeFilterOptions[] = new FilterOption($entityType->value(), $entityType->getLabel());
        }

        $entityTypeFilterOptions[] = new FilterOption('untyped', 'Без связанной сущности');

        return new UsersListPage(
            $params->getUser()->access('users_read'),
            $params->getUser()->access('users_password_change'),
            $params->getUser()->access('users_block'),
            $params->getUser()->access('users_logs_read'),
            $statusFilterOptions,
            $entityTypeFilterOptions
        );
    }
}