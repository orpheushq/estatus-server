<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patientNo')->unsigned();
            $table->bigInteger('organizationId')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('gender');
            $table->date('dob')->nullable();
            $table->string('diabetesType');

            $table->string('password')->nullable()->default(null);
            $table->date('diagnosisDate')->nullable()->default(null);
            $table->string('patientGroup')->nullable()->default(null);
            $table->string('mobileNo')->default("");
            $table->string('emergencyContactName')->default("");
            $table->string('emergencyContactMobile')->default("");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
