<?php

//php artisan make:controller PatientController --api

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Patient;

class PatientController extends Controller
{
    /**
     * Used to find duplicate patients
     * 
     * If any 3 of the 4 attributes (name, gender, dob, mobileNo)
     *
     * @return \Illuminate\Http\Response
     */
    public function findDuplicate(Request $request)
    {
        //
        $attributes = ["name", "gender", "dob", "mobileNo"]; //attributes that would be searched
        $result = collect([]);
        $filters = json_decode($request->filters, TRUE);
        $filters['dob'] = new \DateTime($filters['dob']);
        $filters['dob'] = $filters['dob']->format("Y-m-d");

        $i = 0;
        for ($a=0; $a < count($attributes);$a++) {
            for ($b=0; $b < count($attributes); $b++) {
                for ($c=0; $c < count($attributes); $c++) {
                    if ($attributes[$a] !== $attributes[$b] && $attributes[$b] !== $attributes[$c] && $attributes[$a] !== $attributes[$c]) {
                        $patients = [];
                        switch ($attributes[$a]) {
                            case "gender":
                            case "dob":
                            default: {
                                $patients = Patient::where($attributes[$a] , '=', $filters[$attributes[$a]])->get();
                                break;
                            }
                            case "mobileNo":
                            case "name": {
                                $patients = Patient::where($attributes[$a] , 'like', '%'.$filters[$attributes[$a]].'%')->get();
                                break;
                            }
                        }
                        //if ($i==17) echo "<br>"."<br>".json_encode($patients)."<br>";

                        switch ($attributes[$b]) {
                            case "gender":
                            case "dob":
                            default: {
                                if (count($patients) > 0) {
                                    $patients = $patients->where($attributes[$b] , '=', $filters[$attributes[$b]]);
                                } else {
                                    $patients = Patient::where($attributes[$b] , '=', $filters[$attributes[$b]])->get();
                                }
                                break;
                            }
                            case "mobileNo":
                            case "name": {
                                if (count($patients) > 0) {
                                    $patients = $patients->where($attributes[$b] , 'like', '%'.$filters[$attributes[$b]].'%');
                                    //$patients = $patients->where($attributes[$b] , '=', $filters[$attributes[$b]]);
                                } else {
                                    $patients = Patient::where($attributes[$b] , 'like', '%'.$filters[$attributes[$b]].'%')->get();

                                    
                                }
                                break;
                            }
                        }
                        //if ($i==17) echo json_encode($patients)."<br>";

                        switch ($attributes[$c]) {
                            case "gender":
                            case "dob":
                            default: {
                                if (count($patients) > 0) {
                                    $patients = $patients->where($attributes[$c] , '=', $filters[$attributes[$c]]);
                                }
                                break;
                            }
                            case "mobileNo":
                            case "name": {
                                if (count($patients) > 0) {
                                    $patients = $patients->where($attributes[$c] , 'like', '%'.$filters[$attributes[$c]].'%');
                                }
                                //$patients = $patients->where($attributes[$b] , '=', $filters[$attributes[$b]]);
                                break;
                            }
                        }
                        //if ($i === 17) echo json_encode($patients)."<br>";
                        //if (json_encode($patients) == "{}") $patients = [];
                        //echo $i." ".$attributes[$a]."=".$filters[$attributes[$a]].",".$attributes[$b]."=".$filters[$attributes[$b]].",".$attributes[$c]."=".$filters[$attributes[$c]]." -> ".(is_null($patients) ? 0: count($patients))."<br>";
                        //if ($i==17) echo json_encode($patients)."<br>"."<br>";
                        /*if (is_null($result) && count($patients) > 0) {
                            $result = $patients;
                        } else if(!is_null($result)) {
                            foreach ($patients as $p) {
                                if (!$result->contains($p)) $patients[] = $p;
                            }
                        }*/
                        foreach ($patients as $p) {
                            if (!$result->contains($p)) $result[] = $p;
                        }
                        $i++;
                    }
                }
            }
        }

        // $patients = Patient::where('name' , 'like', '%'.$filters['name'].'%')->get();
        // $patients = $patients->where('dob', $filters['dob']);

        return response($result, 200);
    }

    /**
     * Display a listing of the resource.
     * 
     * If a `filters` parameter is provided (when invoked as a POST request), filters and sorting can be specified
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page)
    {
        //
        $patients = [];

        $filters = json_decode($request->filters, TRUE);

        if (is_null($filters)) {
            $patients = Patient::all();
        } else {
            /**
             * TODO: create a more flexible and scalable approach to query using filters
             */
            $patients = Patient::where('mobileNo' , 'like', '%'.$filters['mobileNo'].'%')->get();
        }

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
        if (isset($values['diagnosisDate'])) $values['diagnosisDate'] = new \DateTime($values['diagnosisDate']);

        $values['password'] = Hash::make($values['password']);

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
        if (isset($newValues['diagnosisDate'])) $newValues['diagnosisDate'] = new \DateTime($newValues['diagnosisDate']);

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
        $patient = Patient::find($id);
        $patient->delete();
        return response($patient, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportingAgeGroup()
    {
        //
        $cYear = intval((new \DateTime())->format("Y"));//var_dump($cYear);exit;
        //0-12 age group
        $results = Patient::whereBetween('dob', [new \DateTime(($cYear-12)."-01-01"), new \DateTime(($cYear-0)."-01-01")])->get();
        $resp[] = count($results);

        //13 - 18 age group
        $results = Patient::whereBetween('dob', [new \DateTime(($cYear-18)."-01-01"), new \DateTime(($cYear-12)."-01-01")])->get();
        $resp[] = count($results);

        //19 - 59 age group
        $results = Patient::whereBetween('dob', [new \DateTime(($cYear-59)."-01-01"), new \DateTime(($cYear-18)."-01-01")])->get();
        $resp[] = count($results);

        //>60 age group
        $results = Patient::where('dob', "<=", new \DateTime(($cYear-59)."-01-01"))->get();
        $resp[] = count($results);

        return response($resp, 200);
    }
}
