<?php

namespace Src\Users\Management;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Src\Users\Management\Commands\BlockUser\BlockUserCommand;
use Src\Users\Management\Commands\BlockUser\BlockUserHandler;
use Src\Users\Management\Commands\CreateUser\CreateUserCommand;
use Src\Users\Management\Commands\CreateUser\CreateUserHandler;
use Src\Users\Management\Commands\DeleteUser\DeleteUserCommand;
use Src\Users\Management\Commands\DeleteUser\DeleteUserHandler;
use Src\Users\Management\Commands\RestoreUser\RestoreUserCommand;
use Src\Users\Management\Commands\RestoreUser\RestoreUserHandler;
use Src\Users\Management\Commands\UnblockUser\UnblockUserCommand;
use Src\Users\Management\Commands\UnblockUser\UnblockUserHandler;
use Src\Users\Management\Commands\UpdateUser\UpdateUserCommand;
use Src\Users\Management\Commands\UpdateUser\UpdateUserHandler;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessCommand;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessHandler;
use Src\Users\Management\Queries\GetUsersSelectItems\GetUsersSelectItemsHandler;
use Src\Users\Management\Queries\GetUsersSelectItems\GetUsersSelectItemsQuery;

final class ManagementProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->extend(Dispatcher::class, function (Dispatcher $dispatcher) {
            $dispatcher->map([
                CreateUserCommand::class => CreateUserHandler::class,
                UpdateUserCommand::class => UpdateUserHandler::class,
                DeleteUserCommand::class => DeleteUserHandler::class,
                RestoreUserCommand::class => RestoreUserHandler::class,
                UpdateUserAccessCommand::class => UpdateUserAccessHandler::class,
                BlockUserCommand::class => BlockUserHandler::class,
                UnblockUserCommand::class => UnblockUserHandler::class,
                GetUsersSelectItemsQuery::class => GetUsersSelectItemsHandler::class,
            ]);

            return $dispatcher;
        });
    }

    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'UsersManagement');
    }
}