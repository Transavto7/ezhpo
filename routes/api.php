<?php

use App\Anketa;
use App\Http\Controllers\Api\SdpoController;
use App\Http\Controllers\ReportContractRefactoringController;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\User;
use App\Point;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/companies/find', 'ApiController@companiesList');
Route::get('/find/{model}', 'ApiController@modelList');

Route::prefix('reports')->group(function () {
    Route::prefix('contract')->group(function () {
        Route::get('/journal', 'ReportControllerContract@getJournalData')->name('api.reports.journal');
        Route::get('/journal/export', 'ReportController@exportJournalData');
        Route::get('/journal_v2', [ReportContractRefactoringController::class, 'getReport']);
        Route::get('/export/journal_v2', [ReportContractRefactoringController::class, 'export']);
    });
    Route::get('getContractsForCompany', 'ReportControllerContract@getContractsForCompany');
});

Route::get('/sync-fields/{model}/{id}', function ($model, $id) {
    return app("App\\$model")->getAutoSyncFieldsFromHashId($id);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users/{role}', function (Request $request) {
        $user = $request->user();

        if ($user->hasRole('terminal')) {
            $user = User::with('roles')->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', 2);
            })->get();

            return response()->json($user);
        }
    });

    Route::post('/get-user-from-token', function (Request $request) {
        $user = $request->user();

        if($user->hasRole('terminal')) {
            if (isset($request->token)) {
                $user = User::where('api_token', $request->token)->first();
            }

            return response()->json($user);
        }
    });

    Route::post('/get-user/{user_id}', function (Request $request) {
        $user = $request->user();

        if($user->hasRole('terminal')) {
            $user_id = $request->user_id;
            $user = User::find($user_id);

            return response()->json($user);
        }
    });

    Route::post('/anketa', 'AnketsController@ApiAddForm')->name('api.addform');

    Route::get('/anketa/{id}', function ($id) {
        $anketa = Anketa::find($id);

        if ($anketa && $anketa->type_anketa == 'pak_queue') {
            if (Carbon::now()->getTimestamp() - Carbon::parse($anketa->created_at)->getTimestamp() > 12) {
                $anketa->type_anketa = 'medic';
                $anketa->save();
            }
        }

        return response()->json($anketa);
    });

    Route::prefix('reports')->group(function () {
        Route::get('/journal', 'ReportController@getJournalData');
        Route::get('/journal/export', 'ReportController@exportJournalData');
        Route::get('/graph_pv', 'ReportController@getGraphPvData');
    });

    // Отображаем ПВ
    Route::get('pvs/{id?}', function () {
        $id = isset(request()->id) ? request()->id : null;

        if($id) {
            $points = Point::find($id);
        } else {
            $points = Point::all();
        }

        return response()->json($points);
    });

    Route::get('/check-prop-one/{prop}/{model}/{val}', 'ApiController@OneCheckProperty');
    Route::get('/check-prop/{prop}/{model}/{val}', 'ApiController@CheckProperty');
    Route::post('/get-previous-odometer/', 'ApiController@getPreviousOdometer');

    Route::put('/update-ddate/{item_model}/{item_id}/{item_field}', 'ApiController@UpdateProperty')->name('updateDDate');
    Route::put('/update-doc/{type}', 'DocsController@update')->name('docs.update');

    Route::post('/fields/visible', 'ApiController@saveFieldsVisible');
});

Route::middleware('auth:api')->prefix('sdpo')->name('sdpo')->group(function () {
    Route::prefix('anketa')->group(function () {
        Route::post('/', 'Api\SdpoController@createAnketa');
        Route::post('/{id}', 'Api\SdpoController@changeType');
        Route::get('/{id}', 'Api\SdpoController@getInspection');
    });

    Route::get('/drivers', 'Api\SdpoController@getDrivers');
    Route::prefix('driver')->group(function () {
        Route::get('/{id}', 'Api\SdpoController@getDriver');
        Route::get('/{id}/prints', 'Api\SdpoController@getPrints');
        Route::post('/{id}/photo', 'Api\SdpoController@setDriverPhoto');
        Route::post('/{id}/phone', 'Api\SdpoController@setDriverPhone');
    });

    Route::get('/pv', 'Api\SdpoController@getPoint');
    Route::get('/stamp', 'Api\SdpoController@getStamp');
    Route::get('/stamps', 'Api\SdpoController@getStamps');
    Route::get('/terminal/verification', 'Api\SdpoController@getTerminalVerification');
    Route::get('/medics', 'Api\SdpoController@getMedics');

    Route::get('/check', 'Api\SdpoController@checkConnaction');
    Route::post('/work/report', [SdpoController::class, 'workReport']);
});

