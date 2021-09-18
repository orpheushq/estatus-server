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
            'dob' => "1997-01-01",
            'diabetesType'=>'Type 1'
        ]);
    }
}
