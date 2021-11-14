<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PatientController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/patients/list/{page?}', [App\Http\Controllers\PatientController::class, 'index']);
//https://stackoverflow.com/a/45558962

Route::prefix('patients')->group(function () {
    Route::get('/list/{page?}', function (Illuminate\Http\Request $request, $page = 0) {
        $ctrl = new PatientController();
        return $ctrl->index($request, $page, FALSE);
    });
    Route::get('/new', function (Illuminate\Http\Request $request) {
        $ctrl = new PatientController();
        return $ctrl->show($request, -1, FALSE);
    });
    Route::get('/{id}', function (Illuminate\Http\Request $request, $id) {
        $ctrl = new PatientController();
        return $ctrl->show($request, $id, FALSE);
    });
    Route::post('/{id}', [PatientController::class, 'update']);
});
