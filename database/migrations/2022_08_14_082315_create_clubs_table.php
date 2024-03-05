<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('alias_id')->nullable();
            $table->string('title');
            $table->string('color')->nullable();
            $table->string('alias_title')->nullable();
            $table->bigInteger('sport_id')->unsigned()->index();
            // $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->bigInteger('country_id')->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->string('image', 2048)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('user_id')->nullable();
            // $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('clubs');
    }
}
