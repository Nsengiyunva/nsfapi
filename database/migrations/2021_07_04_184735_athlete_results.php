<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AthleteResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('athlete_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('athlete_id');
            $table->string('rank');
            $table->string('event');
            $table->string('time');
            $table->string('medal');
            $table->string('pool_length');
            $table->string('age');
            $table->string('competition');
            $table->string('competition_country');
            $table->string('date');

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
        Schema::dropIfExists('athlete_results');
    }
}
