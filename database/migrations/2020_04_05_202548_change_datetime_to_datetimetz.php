<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatetimeToDatetimetz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTimeTz('from')->change();
            $table->dateTimeTz('to')->change();
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dateTimeTz('from')->change();
            $table->dateTimeTz('to')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('from')->change();
            $table->dateTime('to')->change();
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dateTime('from')->change();
            $table->dateTime('to')->change();
        });
    }
}
