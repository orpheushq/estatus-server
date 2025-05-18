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
        Schema::table('statistics', function (Blueprint $table) {
            $table->integer('interest')->default(0);
        });
        Schema::table('region_statistics', function (Blueprint $table) {
            $table->integer('interest')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('interest');
        });
        Schema::table('region_statistics', function (Blueprint $table) {
            $table->dropColumn('interest');
        });
    }
};
