<?php

namespace App\Http\Controllers;

use App\Classes\ProcLand;
use App\Classes\ProcRental;
use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LandController extends Controller
{

    public function upload(Request $request)
    {
        $thisFile = $request->file('dataFile');
        $path = $thisFile->store('land-upload');
        Log::channel('upload')->notice("Land uploading processing started for file ${path}");

        ProcLand::processUpload($path, !is_null($request->post('test')));

        return redirect()->back();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $values = [];
        $validations = [];
        $permissions = [];
        $validator = null;
        $isValid = FALSE;
        $lands = Land::with('property')->where('id', '>', 0);

        $values = $request->all();
        unset($values['_token']);

        // Add validation rules
        $isValid = TRUE;

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response([ "errors" => $validator->errors() ], 422);
            } else {
                return response($lands->get(), 200);
            }
        } else {
            if (!$isValid) {
                return redirect()->back()->withErrors($validator);
            } else {
                // TODO: render view
                return view('lands.list', [ "entities" => $lands->get()]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
