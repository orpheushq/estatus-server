<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('patients')->insert([
            'patientNo' => 1,
            'name' => 'Anne Smith',
            'gender' => 'Female',
            'dob' => "1960-01-01",
            'diabetesType'=>'Type 1'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 2,
            'name' => 'John Doe',
            'gender' => 'Male',
            'dob' => "1997-03-12",
            'diabetesType'=>'Type 2'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 3,
            'name' => 'Jake Fauci',
            'gender' => 'Male',
            'dob' => "1989-02-09",
            'diabetesType'=>'Gestational'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 44,
            'name' => 'Frank Simmons',
            'gender' => 'Male',
            'dob' => "1993-09-20",
            'diabetesType'=>'Type 2'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 98,
            'name' => 'Niel Harris',
            'gender' => 'Male',
            'dob' => "1996-09-20",
            'diabetesType'=>'Gestational'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 983,
            'name' => 'Jennifer Miel',
            'gender' => 'Female',
            'dob' => "2010-12-22",
            'diabetesType'=>'Pre-diabetes'
        ]);

        DB::table('patients')->insert([
            'patientNo' => 1983,
            'name' => 'Tani Myers',
            'gender' => 'Female',
            'dob' => "2007-12-22",
            'diabetesType'=>'Pre-diabetes'
        ]);
    }
}
