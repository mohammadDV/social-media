<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('alias_id')->nullable();
            $table->string('title');
            $table->string('alias_title')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('image', 2048)->nullable();
            $table->bigInteger("sport_id")->unsigned()->index();
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->bigInteger("country_id")->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger("user_id")->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('type')->default(1); // 1 = league | 2 = tournament
            $table->bigInteger("priority")->default(0)->index();
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
        Schema::dropIfExists('leagues');
    }
}
