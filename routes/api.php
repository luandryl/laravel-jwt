<?php

use Illuminate\Http\Request;
use App\User;
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

Route::middleware('jwt.auth')->get('/user', function (Request $req) {
    return response()->json([
        'Myself' => User::where('email', '=', $req->get('email'))->first()
    ], 500);
});

Route::middleware('jwt.auth')->group(function () {
    Route::post('/auth/reset/' ,'Auth\ResetPasswordController@resetPassword');
});

Route::post('/auth/singup/', 'Auth\RegisterController@singup');
Route::post('/auth/login/', 'Auth\LoginController@authenticate');

