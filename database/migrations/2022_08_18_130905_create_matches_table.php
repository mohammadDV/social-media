<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('alias_id')->nullable();
            $table->bigInteger("home_id")->unsigned()->index();
            $table->bigInteger("away_id")->unsigned()->index();
            $table->foreign('home_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('away_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->string('hsc')->default(0);
            $table->string('asc')->default(0);
            $table->string('link')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('date');
            $table->string('hour');
            $table->bigInteger("step_id")->unsigned()->index();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');
            $table->bigInteger("user_id")->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('matches');
    }
}
