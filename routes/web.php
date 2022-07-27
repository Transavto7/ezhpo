<?php

use App\Http\Middleware\ {
    CheckAdmin, CheckManager
};

// Маршруты статичных и главных страниц
Route::get('/', 'IndexController@RenderIndex')->name('index');
Route::get('/releases', 'IndexController@RenderReleases')->name('releases');

Route::get('/fix', function() {
    \App\Anketa::whereIn('type_view', ['Предрейсовый', 'Предсменный', 'предрейсовый/Предсменный', 'Предрейсовый/предсменный',
        'предрейсовый/предсменный'])->update(
        [
            'type_view' => 'Предрейсовый/Предсменный'
        ]
    );
    \App\Anketa::whereIn('type_view', ['Послерейсовый', 'Послесменный', 'послерейсовый/Послесменный', 'Послерейсовый/послесменный',
        'послерейсовый/послесменный'])->update(
        [
            'type_view' => 'Послерейсовый/Послесменный'
        ]
    );
});

Route::get('/show-video', function () {
    $url = isset($_GET['url']) ? $_GET['url'] : '';

    return view('showVideo', [
        'video' => $url
    ]);
})->name('showVideo');

Route::get('/show-edit-element-modal/{model}/{id}', 'IndexController@ShowEditModal')->name('showEditElementModal');

/**
 * API-маршруты
 */

// Сброс пункта выпуска
Route::get('/api/pv-reset/$2y$10$I.RBe8HbmRj2xwpRFWl15OHmWRIMz98RXy1axcK8Jrnx', 'ApiController@ResetAllPV')->name('api.resetpv');
Route::get('/api/getField/{model}/{field}/{default_value?}', 'IndexController@GetFieldHTML');

Route::prefix('snippet')->group(function () {
    Route::get('/update-pak-fields/$2y$10$I.RBe8HbmRj2xwpRFWl15OHmWRIMz98RXy1axcK8Jrnx', function () {
        $ankets = \App\Anketa::where('is_pak', 1)
            ->where('type_anketa', 'medic')
            ->update(array( 'flag_pak' => 'СДПО А' ));

        return response()->json($ankets);
    });

    Route::get('/driver-to-user-all/$2y$10$I.RBe8HbmRj2xwpRFWl15OHmWRIMz98RXy1axcK8Jrnx', function () {
        $drivers = DB::select('select * from drivers WHERE hash_id NOT IN (SELECT login FROM users)');

        if(count($drivers) <= 0) {
            echo 'все водители добавлены как пользователи';
        }

        foreach($drivers as $driver) {
            $pv_id = isset($driver->company_id) ? \App\Company::where('id', $driver->company_id)->first() : 0;

            if($pv_id) {
                $pv_id = isset($pv_id->pv_id) ? $pv_id->pv_id : 0;
            }

            if(!$pv_id) {
                $pv_id = 0;
            }

            $register = new \App\Http\Controllers\Auth\RegisterController();
            $created = $register->create([
                'hash_id' => $driver->hash_id . rand(999,99999),
                'email' => $driver->hash_id . 'driver@ta-7.ru',
                'login' => $driver->hash_id,
                'password' => $driver->hash_id,
                'name' => $driver->fio,
                'pv_id' => $pv_id,
                'role' => 3
            ]);

            echo '['.$driver->hash_id.'] Создан Водитель Как Пользователь ' . '  ('.count($drivers).')<br/>';
        }
    });

//    Route::get('/register-user-admin/$2y$10$I.RBe8HbmRj2xwpRFWl15OHmWRIMz98RXy1axcK8Jrnx', function () {
//        $reg = new \App\Http\Controllers\Auth\RegisterController();
//        return response()->json($reg->create([
//            'name' => 'ADMIN',
//            'email' => 'webmazaretto@gmail.com',
//            'role' => 777,
//            'password' => 'webmazaretto@gmail.com',
//            'login' => 'webmazaretto@gmail.com'
//        ]));
//    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('driver-dashboard', function () {
        return view('pages.driver');
    })->name('page.driver');

    Route::get('/add-client', 'IndexController@RenderAddClient')->name('pages.add_client');

    Route::get('driver-bdd', function () {
        $instrs = \App\Instr::where('active', 1)->orderBy('sort', 'asc')->get();
        $pv_id = \App\Driver::where('hash_id', auth()->user()->id)->first();

        if($pv_id) {
            $pv_id = \App\Company::find($pv_id->company_id);

            if($pv_id) {
                $pv_id = $pv_id->pv_id;
            } else {
                $pv_id = 0;
            }
        } else {
            $pv_id = 0;
        }

        return view('pages.driver_bdd', [
            'instrs' => $instrs,
            'pv_id' => $pv_id
        ]);
    })->name('page.driver_bdd');

    Route::prefix('profile')->group(function () {
        Route::post('/anketa', 'AnketsController@AddForm')->name('addAnket');

        Route::get('delete-avatar', 'ProfileController@DeleteAvatar')->name('deleteAvatar');
        Route::get('/', 'ProfileController@RenderIndex')->name('profile');
        Route::post('/', 'ProfileController@UpdateData')->name('updateProfile');
    });
});

/**
 * Профиль, анкета, авторзация
 */
Route::middleware(['auth', \App\Http\Middleware\CheckDriver::class])->group(function () {
    Route::get('/home/filters', 'HomeController@getFilters');
    Route::get('/home/{type_ankets?}/filters', 'HomeController@getFilters')->name('home.filters');
    Route::get('/home/{type_ankets?}', 'HomeController@index')->name('home');

    Route::prefix('profile')->group(function () {
        Route::get('/anketa', 'IndexController@RenderForms')->name('forms');
    });

    Route::prefix('docs')->group(function () {
        Route::get('{type}/{anketa_id}', 'DocsController@Get')->name('docs.get');
    });

    // Рендер элемента (водитель, компания и т.д.)
    Route::get('/elements/{type}', 'IndexController@RenderElements')->name('renderElements');
});


/**
 * Элементы CRM
 */
Route::middleware(['auth', \App\Http\Middleware\CheckDriver::class])->group(function () {
    Route::prefix('elements')->group(function () {
        // Удаление элемента (водитель, компания и т.д.)
        Route::get('/{type}/{id}', 'IndexController@RemoveElement')->name('removeElement');
        // Добавление элемента (водитель, компания и т.д.)
        Route::post('/{type}', 'IndexController@AddElement')->name('addElement');
        // Обновление элемента (водитель, компания и т.д.)
        Route::post('/{type}/{id}', 'IndexController@updateElement')->name('updateElement');

        // Удаление файла
        Route::get('/delete-file/{model}/{id}/{field}', 'IndexController@DeleteFileElement')->name('deleteFileElement');
    });

    Route::post('/elements-import/{type}', 'IndexController@ImportElements')->name('importElements');
    Route::get('/elements-syncdata/{fieldFindId}/{fieldFind}/{model}/{fieldSync}/{fieldSyncValue?}', 'IndexController@SyncDataElement')->name('syncDataElement');

    Route::prefix('anketa')->group(function () {
        Route::delete('/{id}', 'AnketsController@Delete')->name('forms.delete');
        Route::post('/{id}', 'AnketsController@Update')->name('forms.update');
        Route::get('/{id}', 'AnketsController@Get')->name('forms.get');

        Route::get('/change-pak-queue/{id}/{admitted}', 'AnketsController@ChangePakQueue')->name('changePakQueue');
        Route::get('/change-resultdop-queue/{id}/{result_dop}', 'AnketsController@ChangeResultDop')->name('changeResultDop');
    });

    Route::prefix('report')->group(function () {
        Route::get('journal', 'ReportController@ShowJournal')->name('report.journal');
        Route::get('{type_report}', 'ReportController@GetReport')->name('report.get');
    });

    // Сохранение полей в HOME
    Route::post('/save-fields-home/{type_ankets}', 'HomeController@SaveCheckedFieldsFilter')->name('home.save-fields');

    Route::get('/anketa-trash/{id}/{action}', 'AnketsController@Trash')->name('forms.trash');
});

/**
 * Панель администратора
 */
Route::middleware(['auth', CheckAdmin::class])->group(function () {

    Route::prefix('admin')->group(function () {

        // Модернизация пользователей
        Route::get('/users', 'AdminController@ShowUsers')->name('adminUsers');
        Route::post('/users', 'AdminController@CreateUser')->name('adminCreateUser');
        Route::get('/users/{id}', 'AdminController@DeleteUser')->name('adminDeleteUser');
        Route::post('/users/{id}', 'AdminController@UpdateUser')->name('adminUpdateUser');

        Route::prefix('settings')->group(function () {
            Route::get('/import_admin_data_settings', 'SettingsController@ImportSystemSettings');
            Route::get('/', 'SettingsController@RenderSystemSettings')->name('systemSettings');
            Route::post('/', 'SettingsController@UpdateSystemSetting')->name('systemSettings.update');
        });

    });

});

// Маршруты авторизации
Auth::routes();

