<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('statistics', function (Blueprint $table) {
            $table->decimal('price',14,0)->change();
        });
        Schema::table('lands', function (Blueprint $table) {
            $table->decimal('size', 10, 2)->change();
        });
        Schema::table('region_statistics', function (Blueprint $table) {
            $table->decimal('price',14,0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('statistics', function (Blueprint $table) {
            $table->integer('price')->change();
        });
        Schema::table('lands', function (Blueprint $table) {
            $table->decimal('size', 5, 2)->change();
        });
        Schema::table('region_statistics', function (Blueprint $table) {
            $table->integer('price')->change();
        });
    }
};
