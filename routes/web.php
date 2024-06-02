<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Http\Request;
use \App\Http\Controllers\PropertyController;
use \App\Http\Controllers\RentalController;
use \App\Http\Controllers\LandController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/patients/list/{page?}', [App\Http\Controllers\PatientController::class, 'index']);
//https://stackoverflow.com/a/45558962

Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('properties', PropertyController::class)->middleware('can:properties');

    Route::post('rentals/upload', [RentalController::class, 'upload'])->middleware('can:properties')->name("rentals.upload");
    Route::resource('rentals', RentalController::class)->middleware('can:properties');

    Route::post('lands/triggerAlertNewProperties', [LandController::class, 'triggerAlertNewProperties'])->middleware('can:properties')->name("lands.triggerAlertNewProperties");
    Route::post('lands/upload', [LandController::class, 'upload'])->middleware('can:properties')->name("lands.upload");
    Route::resource('lands', LandController::class)->middleware('can:properties');
});

Route::prefix('logs')->middleware('auth')->group(function () {
    Route::get('login', [LogController::class, 'login'])->middleware('can:view login logs')->name("logs.login");
    Route::get('explorer', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])
        ->middleware('can:view log explorer')
        ->name("logs.explorer");
});

Route::prefix('patients')->middleware('auth')->group(function () {
});
