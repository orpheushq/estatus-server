<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

/* Public Routes */
Route::get('/test', function (Request $request) {
    return json_encode($request, true);
});

Route::post('/auth/login', [AuthController::class, 'login']);

/* Protected Routes */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/secure/test', function (Request $request) {
        return response(["user" => $request->user()], 200);
    });

    Route::get('/patients/{page}', [PatientController::class, 'index']);
    Route::apiResource('patients', PatientController::class);
});