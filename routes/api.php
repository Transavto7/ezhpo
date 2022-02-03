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

Route::middleware('auth:api')->post('/get-user/{user_id}', function (Request $request) {
    $user = $request->user();

    if($user->role >= 777) {
        $user_id = $request->user_id;
        $user = User::find($user_id);

        return response()->json($user);
    }
});

Route::middleware('auth:api')->group(function () {
    // Отображаем ПВ
    Route::get('pvs', function () {
        $points = Point::all();

        return response()->json($points);
    });

    Route::post('/anketa', 'AnketsController@ApiAddForm')->name('api.addform');
    Route::get('/check-prop/{prop}/{model}/{val}', 'ApiController@CheckProperty');

    Route::post('/field-history', 'FieldHistoryController@save');
    Route::put('/update-ddate/{item_model}/{item_id}/{item_field}', 'ApiController@UpdateProperty')->name('updateDDate');
});
