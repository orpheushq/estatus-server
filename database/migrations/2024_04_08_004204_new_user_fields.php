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
        Schema::table('users', function (Blueprint $table) {
            $table->text('phone_number');
            $table->boolean('is_buyer')->default(false);
            $table->boolean('is_seller')->default(false);
            $table->enum('subscription', ['free', 'premium'])->default('free');
            $table->boolean('sms_alert')->default(false);
            $table->boolean('email_alert')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('is_buyer');
            $table->dropColumn('is_seller');
            $table->dropColumn('subscription');
            $table->dropColumn('sms_alert');
            $table->dropColumn('email_alert');
        });
    }
};
