<?php

namespace Src\Users\Management\Queries\GetUserShowPage;

final class GetUserShowPageHandler
{
    public function handle(GetUserShowPageQuery $params): UserShowPage
    {
        return new UserShowPage(
            $params->getUser()->access('users_read'),
            $params->getUser()->access('users_password_change'),
            $params->getUser()->access('users_block'),
            $params->getUser()->access('users_access_change'),
            $params->getUser()->access('users_logs_read')
        );
    }
}