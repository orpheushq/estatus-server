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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('type');
            $table->integer('propertyable_id');
            $table->string('propertyable_type');
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
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('size', 4, 2);
            $table->dropColumn('propertyable_id');
            $table->dropColumn('propertyable_type');
        });
    }
};
