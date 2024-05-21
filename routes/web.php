<?php

use App\Http\Controllers\ReportContractRefactoringController;
use App\Http\Middleware\CheckDriver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('fix/types', 'IndexController@deprecated');

Route::get('/', 'IndexController@RenderIndex')->name('index');
Route::get('index', 'IndexController@RenderHome')->name('index');

Route::get('show-video', 'IndexController@showVideo')->name('showVideo');
Route::get('show-edit-element-modal/{model}/{id}', 'IndexController@ShowEditModal')->name('showEditElementModal');

/**
 * API-маршруты
 */
$techToken = '$2y$10$I.RBe8HbmRj2xwpRFWl15OHmWRIMz98RXy1axcK8Jrnx';

Route::prefix('api')->group(function () use ($techToken) {
    Route::get("pv-reset/$techToken", 'ApiController@ResetAllPV')->name('api.resetpv');
    Route::get('getField/{model}/{field}/{default_value?}', 'IndexController@GetFieldHTML');
});

Route::prefix('snippet')->group(function () use ($techToken) {
    Route::get("update-pak-fields/$techToken", 'IndexController@deprecated');
    Route::get("driver-to-user-all/$techToken", 'IndexController@deprecated');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('v-search')->group(function () {
        Route::get('companies', '\App\Helpers\VSelect@companies');
        Route::get('cars', '\App\Helpers\VSelect@cars');
        Route::get('drivers', '\App\Helpers\VSelect@drivers');
        Route::get('services', '\App\Helpers\VSelect@services');
        Route::get('our_companies', '\App\Helpers\VSelect@our_companies');
    });

    Route::prefix('contract')->group(function () {
        Route::get('/', 'ContractController@view');
        Route::put('restore/{id}', 'ContractController@restore');
        Route::get('index', 'ContractController@index');
        Route::get('getOne', 'ContractController@getOne');
        Route::post('update', 'ContractController@update');
        Route::get('create', 'ContractController@create');
        Route::delete('{id}', 'ContractController@destroy');
        Route::get('getTypes', 'ContractController@getTypes');
        Route::post('getCarsByCompany/{id}', 'ContractController@getCarsByCompany');
        Route::post('getDriversByCompany/{id}', 'ContractController@getDriversByCompany');
        Route::post('getAvailableForCompany', 'ContractController@getAvailableForCompany');
    });

    Route::get('driver-dashboard', function () { return view('pages.driver'); })->name('page.driver');

    Route::get('add-client', 'IndexController@RenderAddClient')->name('pages.add_client');

    Route::prefix('driver-bdd')->group(function () {
        Route::get('/', 'BddController@get')->name('page.driver_bdd');
        Route::post('/', 'BddController@store')->name('bdd.store');
    });

    Route::prefix('profile')->group(function () {
        Route::post('anketa', 'AnketsController@AddForm')->name('addAnket');
        Route::get('delete-avatar', 'ProfileController@DeleteAvatar')->name('deleteAvatar');
        Route::get('/', 'ProfileController@RenderIndex')->name('profile');
        Route::post('/', 'ProfileController@UpdateData')->name('updateProfile');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@index')->name('users');
        Route::post('/', 'UserController@destroy');
        Route::get('fetchCompanies', 'UserController@fetchCompanies');
        Route::get('fetchRoleData', 'UserController@fetchRoleData');
        Route::get('fetchUserData', 'UserController@fetchUserData');
        Route::post('return_trash', 'UserController@returnTrash');
        Route::get('saveUser', 'UserController@saveUser');
    });

    Route::prefix('terminals')->as('terminals.')->group(function () {
        Route::get('/', 'TerminalController@index')->name('index');
        Route::post('/', 'TerminalController@update')->name('update');
        Route::get('status', 'TerminalController@getConnectionStatus')->name('status');
        Route::get('to-check', 'TerminalController@terminalsToCheck')->name('to-check');
    });

    Route::resource('roles', 'RoleController');
    Route::prefix('roles')->as('roles.')->group(function () {
        Route::post('return_trash', 'RoleController@returnTrash');
    });

    Route::resource('field/prompt', 'FieldPromptController');
    Route::prefix('field/prompt')->as('prompt.')->group(function () {
        Route::any('filter', 'FieldPromptController@getAll');
    });

    Route::resource('stamp', 'StampController')->except(['show']);
    Route::prefix('stamp')->as('stamp.')->group(function () {
        Route::any('filter', 'StampController@getAll');
        Route::any('find', 'StampController@find');
    });

    Route::prefix('agreement')->group(function () {
        Route::get('/', 'IndexController@agreement');
        Route::post('/', 'IndexController@acceptAgreement');
    });

    Route::middleware([CheckDriver::class])->group(function () {
        /**
         * Профиль, анкета, авторзация
         */
        Route::prefix('home')->group(function () {
            Route::get('filters', 'HomeController@getFilters');
            Route::get('{type_ankets?}/filters', 'HomeController@getFilters')->name('home.filters');
            Route::get('pak_queue', 'PakController@index');
            Route::get('{type_ankets?}', 'HomeController@index')->name('home');
        });

        Route::prefix('pak')->as('pak.')->group(function () {
            Route::get('/', 'PakController@index')->name('index');
            Route::get('list', 'PakController@list')->name('list');
        });

        Route::prefix('profile')->group(function () {
            Route::get('anketa', 'IndexController@RenderForms')->name('forms');
        });

        Route::prefix('docs')->as('docs.')->group(function () {
            Route::get('{type}/{anketa_id}/pdf', 'DocsController@getPdf')->name('get.pdf');
            Route::post('{type}/{anketa_id}/set', 'DocsController@setPdf')->name('add.pdf');
            Route::any('{type}/{anketa_id}/delete', 'DocsController@delete')->name('delete');
            Route::get('{type}/{anketa_id}', 'DocsController@Get')->name('get');
        });

        /**
         * Элементы CRM
         */
        Route::prefix('elements')->group(function () {
            Route::get('/export/{type}', 'Elements\ExportElementController')->name('exportElement');
            Route::post('/import', 'Elements\ImportElementController')->name('importElement');

            Route::get('{type}', 'IndexController@RenderElements')->name('renderElements');
            Route::get('{type}/{id}', 'IndexController@RemoveElement')->name('removeElement');
            Route::post('{type}', 'IndexController@AddElement')->name('addElement');
            Route::post('{type}/{id}', 'IndexController@updateElement')->name('updateElement');
            Route::get('{type}/sync/{id}', 'IndexController@syncElement')->name('syncElement');
            Route::get('delete-file/{model}/{id}/{field}', 'IndexController@DeleteFileElement')->name('deleteFileElement');
        });

        Route::post('elements-import/{type}', 'IndexController@ImportElements')->name('importElements');
        Route::get('elements-syncdata/{fieldFindId}/{fieldFind}/{model}/{fieldSync}/{fieldSyncValue?}', 'IndexController@SyncDataElement')->name('syncDataElement');

        Route::prefix('anketa')->group(function () {
            Route::get('print/{id}', 'AnketsController@Print')->name('forms.print');
            Route::delete('{id}', 'AnketsController@Delete')->name('forms.delete');
            Route::post('{id}', 'AnketsController@Update')->name('forms.update');
            Route::get('{id}', 'AnketsController@Get')->name('forms.get');
            Route::get('change-pak-queue/{id}/{admitted}', 'AnketsController@ChangePakQueue')->name('changePakQueue');
            Route::get('change-resultdop-queue/{id}/{result_dop}', 'AnketsController@ChangeResultDop')->name('changeResultDop');
        });

        Route::prefix('report')->as('report.')->group(function () {
            Route::get('getContractsForCompany_v2', [ReportContractRefactoringController::class, 'getContractsForCompany']);
            Route::get('journal', 'ReportController@ShowJournal')->name('journal');
            Route::get('journal_new',[ReportContractRefactoringController::class, 'index'])->name('company_service');
            Route::get('{type_report}', 'ReportController@GetReport')->name('get');

            Route::prefix('dynamic')->as('dynamic.')->group(function () {
                Route::get('medic', 'ReportController@getDynamicMedic')->name('medic');
                Route::get('tech', 'ReportController@getDynamicTech')->name('tech');
                Route::get('all', 'ReportController@getDynamicAll')->name('all');
            });
        });

        Route::post('save-fields-home/{type_ankets}', 'HomeController@SaveCheckedFieldsFilter')->name('home.save-fields');
        Route::get('anketa-trash/{id}/{action}', 'AnketsController@Trash')->name('forms.trash');
        Route::get('anketa-mass-trash', 'AnketsController@MassTrash')->name('forms.mass-trash');
    });

    /**
     * Панель администратора
     */
    Route::prefix('admin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', 'AdminController@ShowUsers')->name('adminUsers');
            Route::post('/', 'AdminController@CreateUser')->name('adminCreateUser');
            Route::get('{id}', 'AdminController@DeleteUser')->name('adminDeleteUser');
            Route::post('{id}', 'AdminController@UpdateUser')->name('adminUpdateUser');
        });

        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('/', 'SettingsController@index')->name('index');
            Route::post('/', 'SettingsController@update')->name('update');
        });
    });
});

Auth::routes();

