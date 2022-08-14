<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LoginLog;

class LogController extends Controller
{
    //
    private $data;

    function __construct(Request $request) {
        switch ($request->segment(2)) {
            case "login": {
                $this->data = LoginLog::where('id', '>', 0)->with('user:id,name,email');
                break;
            }
        }
    }
    public function login(Request $request)
    {
        /**
         * List login logs
         */
        // dd($this->data->get());
        return view('logs.login', [ "entities" => $this->data->get()]);
    }
}
