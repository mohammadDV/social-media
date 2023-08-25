<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_club', function (Blueprint $table) {
            $table->bigInteger("league_id")->unsigned()->index();
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
            $table->bigInteger("club_id")->unsigned()->index();
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->Integer("points")->default(0);
            $table->Integer("games_count")->default(0);
            $table->primary(['league_id','club_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('league_club');
    }
}
