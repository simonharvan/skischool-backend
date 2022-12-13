<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->unsignedBigInteger('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lesson_payouts', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->unique();
            $table->foreign('lesson_id')->references('id')->on('lessons');
            $table->unsignedBigInteger('payout_id');
            $table->foreign('payout_id')->references('id')->on('payouts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_payouts');
        Schema::dropIfExists('payouts');
    }
}
