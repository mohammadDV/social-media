<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStepClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('step_club', function (Blueprint $table) {
            $table->bigInteger("club_id")->unsigned()->index();
            $table->bigInteger("step_id")->unsigned()->index();
            $table->Integer("points")->default(0);
            $table->Integer("games_count")->default(0);
            $table->primary(['step_id','club_id']);
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('step_club');
    }
}
