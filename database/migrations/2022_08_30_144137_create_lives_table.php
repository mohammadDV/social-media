<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('teams');
            $table->string('date');
            $table->string('hour');
            $table->string('link')->nullable();
            $table->string('info')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer("priority")->index()->default(0);
            $table->bigInteger("user_id")->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('lives');
    }
}
