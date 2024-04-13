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
        Schema::table('properties', function (Blueprint $table) {
            $table->text('maplink')->nullable()->default(NULL);
            $table->text('raw_maplink')->nullable()->default(NULL);
            $table->text('address')->nullable()->default(NULL);
            $table->text('raw_address')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('maplink');
            $table->dropColumn('raw_maplink');
            $table->dropColumn('address');
            $table->dropColumn('raw_address');
        });
    }
};
