<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Http\Request;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/patients/list/{page?}', [App\Http\Controllers\PatientController::class, 'index']);
//https://stackoverflow.com/a/45558962

Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
});

Route::prefix('patients')->middleware('auth')->group(function () {
});
