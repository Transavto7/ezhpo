<?php

use Illuminate\Http\Request;

use App\Http\Middleware\{
    VerifyApiToken
};

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

Route::get('reports/journal', 'ReportController@getJournalData')->name('api.reports.journal');
Route::get('reports/journal/export', 'ReportController@exportJournalData')->name('api.reports.journal.export');

Route::get('/sync-fields/{model}/{id}', function ($model, $id) {
    $data = app("App\\$model")->getAutoSyncFieldsFromHashId($id);

    return $data;
});

Route::middleware('auth:api')->get('/users/{role}', function (Request $request) {
    $user = $request->user();
    $roleRequest = $request->role;

    $validRoles = [
        '2' => true // medic
    ];

    if($user->role >= 777 && isset($validRoles[$roleRequest])) {
        $user = User::where('role', $roleRequest)->get();

        return response()->json($user);
    }
});

Route::middleware('auth:api')->post('/get-user-from-token', function (Request $request) {
    $user = $request->user();

    if($user->role >= 777) {
        $token = $request->all();
        $token = isset($token['token']) ? $token['token'] : '';
        $user = User::where('api_token', $token)->first();

        return response()->json($user);
    }
});


Route::middleware('auth:api')->post('/get-user/{user_id}', function (Request $request) {
    $user = $request->user();

    if($user->role >= 777) {
        $user_id = $request->user_id;
        $user = User::find($user_id);

        return response()->json($user);
    }
});

Route::middleware('auth:api')->group(function () {

    Route::get('anketa/{id}', function ($id) {
        $anketa = \App\Anketa::find($id);

        return response()->json($anketa);
    });

    Route::get('report/{type_report}', 'ReportController@ApiGetReport')->name('api.getReport');

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

    Route::prefix('notify')->group(function () {

        Route::get('/', function () {
            $user = request()->user();

            $notifies = \Illuminate\Support\Facades\DB::select('select * from notifies where role=? and id NOT IN (select notify_id from notify_statuses where user_id=?)', [$user->role, $user->id]);

            return response()->json($notifies);
        })->name('api.notify.get');

        Route::post('clear', function () {
            $user = request()->user();
            $notifies = \App\Notify::where('role', $user->role)->get();

            foreach($notifies as $notify) {
                \App\NotifyStatuse::create([
                    'user_id' => $user->id,
                    'notify_id' => $notify->id
                ]);
            }
        })->name('api.notify.clear');

    });

    Route::post('/anketa', 'AnketsController@ApiAddForm')->name('api.addform');
    Route::get('/check-prop-one/{prop}/{model}/{val}', 'ApiController@OneCheckProperty');
    Route::get('/check-prop/{prop}/{model}/{val}', 'ApiController@CheckProperty');

    Route::post('/field-history', 'FieldHistoryController@save');
    Route::put('/update-ddate/{item_model}/{item_id}/{item_field}', 'ApiController@UpdateProperty')->name('updateDDate');

    Route::post('/fields/visible', 'ApiController@saveFieldsVisible');
});
