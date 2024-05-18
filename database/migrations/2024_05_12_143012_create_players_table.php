<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('alias_id')->nullable();
            $table->string('alias_title')->nullable();
            $table->string('description')->nullable();
            $table->string('age')->nullable();
            $table->string('height')->nullable();
            $table->string('foot')->nullable();
            $table->string('number')->nullable();
            $table->enum('position', ['Goalkeeper', 'Defender', 'Midfielder', 'Forward']);
            $table->bigInteger("club_id")->nullable();
            $table->bigInteger("sport_id")->unsigned()->index();
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->bigInteger("country_id")->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->string('image', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};