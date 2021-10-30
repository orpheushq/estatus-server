<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Patient;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Patient::create([
            'patientNo' => 1,
            'name' => 'Anne Smith',
            'gender' => 'Female',
            'dob' => "1960-01-01",
            'diabetesType'=>'Type 1'
        ]);

        Patient::create([
            'patientNo' => 2,
            'name' => 'John Doe',
            'gender' => 'Male',
            'dob' => "1997-03-12",
            'diabetesType'=>'Type 2'
        ]);

        Patient::create([
            'patientNo' => 3,
            'name' => 'Jake Fauci',
            'gender' => 'Male',
            'dob' => "1989-02-09",
            'diabetesType'=>'Gestational'
        ]);

        Patient::create([
            'patientNo' => 44,
            'name' => 'Frank Simmons',
            'gender' => 'Male',
            'dob' => "1993-09-20",
            'diabetesType'=>'Type 2'
        ]);

        Patient::create([
            'patientNo' => 98,
            'name' => 'Niel Harris',
            'gender' => 'Male',
            'dob' => "1996-09-20",
            'diabetesType'=>'Gestational'
        ]);

        Patient::create([
            'patientNo' => 983,
            'name' => 'Jennifer Miel',
            'gender' => 'Female',
            'dob' => "2010-12-22",
            'diabetesType'=>'Pre-diabetes'
        ]);

        Patient::create([
            'patientNo' => 1983,
            'name' => 'Tani Myers',
            'gender' => 'Female',
            'dob' => "2007-12-22",
            'diabetesType'=>'Pre-diabetes'
        ]);

        //gender, dob and name match
        Patient::create([
            'patientNo' => 3221,
            'name' => 'Paul',
            'gender' => 'Male',
            'dob' => "1990-12-22",
            'diabetesType'=>'Pre-diabetes',
            'mobileNo' => '766634555'
        ]);
        Patient::create([
            'patientNo' => 11241,
            'name' => 'Paul Kirkland',
            'gender' => 'Male',
            'dob' => "1990-12-22",
            'diabetesType'=>'Pre-diabetes',
            'mobileNo' => '766634546'
        ]);
        Patient::create([
            'patientNo' => 111141,
            'name' => 'Paul Nate',
            'gender' => 'Male',
            'dob' => "1990-12-22",
            'diabetesType'=>'Pre-diabetes',
            'mobileNo' => '766634000'
        ]);
        //gender, dob and mobileNo
        Patient::create([
            'patientNo' => 5631,
            'name' => 'Era Goldsworth',
            'gender' => 'Female',
            'dob' => "1992-08-12",
            'diabetesType'=>'Pre-diabetes',
            'mobileNo' => '711631523'
        ]);
        Patient::create([
            'patientNo' => 9837,
            'name' => 'Nira',
            'gender' => 'Female',
            'dob' => "1992-08-12",
            'diabetesType'=>'Pre-diabetes',
            'mobileNo' => '711631523'
        ]);
    }
}
