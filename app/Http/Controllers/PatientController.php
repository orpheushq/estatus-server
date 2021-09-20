<?php

//php artisan make:controller PatientController --api

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page)
    {
        //
        $patients = Patient::all();
        return response($patients, 200);
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
        $values = json_decode($request->entity, TRUE);
        $values['dob'] = new \DateTime($values['dob']);

        $newEntity = Patient::create($values);

        return response($newEntity, 200);
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
        $patient = Patient::find($id);
        return response($patient, 200);
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
        $patient = Patient::find($id);
        $newValues = json_decode($request->entity, TRUE);

        $newValues['dob'] = new \DateTime($newValues['dob']);

        foreach ($newValues as $k => $v) {
            $patient[$k] = $v;
        }

        $patient->save();
        return response(Patient::find($id), 200);
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
