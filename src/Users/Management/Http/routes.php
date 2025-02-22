<?php

use Illuminate\Support\Facades\Route;
use Src\Users\Management\Http\Controllers\BlockUserController;
use Src\Users\Management\Http\Controllers\ChangeUserPasswordController;
use Src\Users\Management\Http\Controllers\GetPermissionItemsController;
use Src\Users\Management\Http\Controllers\GetPermissionsByRolesController;
use Src\Users\Management\Http\Controllers\GetRoleItemsController;
use Src\Users\Management\Http\Controllers\GetUserAccessDataController;
use Src\Users\Management\Http\Controllers\GetUserItemController;
use Src\Users\Management\Http\Controllers\GetUsersSelectItemsController;
use Src\Users\Management\Http\Controllers\GetUsersTableItemsController;
use Src\Users\Management\Http\Controllers\ShowUserPageController;
use Src\Users\Management\Http\Controllers\UnblockUserController;
use Src\Users\Management\Http\Controllers\UpdateUserAccessController;
use Src\Users\Management\Http\Controllers\UsersListPageController;

Route::middleware(['web', 'auth'])->prefix('users/management')->as('users.management.')->group(function () {
    Route::get('/', UsersListPageController::class)->name('list-page');
    Route::post('/table-items', GetUsersTableItemsController::class)->name('list-table');
    Route::get('/{id}', ShowUserPageController::class)->name('show-page');
    Route::get('/{id}/item', GetUserItemController::class)->name('item');
    Route::get('/{id}/access-data', GetUserAccessDataController::class)->name('access-data');
    Route::post('/{id}/unblock', UnblockUserController::class)->name('unblock-user');
    Route::post('/{id}/block', BlockUserController::class)->name('block-user');
    Route::post('/{id}/change-password', ChangeUserPasswordController::class)->name('change-password');
    Route::post('/{id}/update-access', UpdateUserAccessController::class)->name('update-access');
    Route::get('/dictionary/permissions', GetPermissionItemsController::class)->name('permissions');
    Route::get('/dictionary/roles', GetRoleItemsController::class)->name('roles');
    Route::get('/permissions/by-roles', GetPermissionsByRolesController::class)->name('permissions-by-roles');
    Route::post('/select/users', GetUsersSelectItemsController::class)->name('select.users');
});