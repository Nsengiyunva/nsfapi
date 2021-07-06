<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Applications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('entity_type');
            $table->string('specify_other_type');
            $table->string('name');

            $table->string('signed')->nullable();
            $table->string('compliant')->nullable();
            $table->string('completed')->nullable();
            $table->string('composition')->nullable();
            $table->string('calendar')->nullable();
            $table->string('payment')->nullable();
            $table->string('any_other')->nullable();

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
        Schema::dropIfExists('applications');
    }
}
