<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Classes\CustomRoute;

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
Route::post('/auth/gauth', [AuthController::class, 'gauth']);

Route::get('/util/lists', [PropertyController::class, 'getLists']);

Route::get('/properties/addresses', [PropertyController::class, 'getUniqueAddresses']);

Route::apiResource('properties', PropertyController::class);
Route::apiResource('regions', RegionController::class);
Route::get('/regions/{region}', [RegionController::class, 'show']);

/* Protected Routes */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/verifyToken', [AuthController::class, 'verifyToken']);

    CustomRoute::profileResource('profile', UserController::class);

    Route::get('/secure/test', function (Request $request) {
        return response(["user" => $request->user()], 200);
    });
});
