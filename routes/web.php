<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/patient/list/{page?}', [App\Http\Controllers\PatientController::class, 'index']);
//https://stackoverflow.com/a/45558962
Route::get('/patients/list/{page?}', function (Illuminate\Http\Request $request, $page = 0) {
    $ctrl = new \App\Http\Controllers\PatientController();
    return $ctrl->index($request, $page, FALSE);
});
Route::get('/patients/{id}', function (Illuminate\Http\Request $request, $id) {
    return "dsdsd";
});
