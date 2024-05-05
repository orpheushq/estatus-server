<?php
namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CustomRoute {

    public static function profileResource($label, $class) {

        Route::patch('/'.$label, function (Request $request) use ($class) {
            $ctrl = new $class;
            return $ctrl->update($request, $request->user()->id);
        });

        Route::apiResource($label, $class);
    }

}
