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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('nickname')->nullable();
            $table->foreignId('role_id')->unsigned();
            $table->foreignId('type')->default(1); // 1 : normal user // 2 : admin user
            $table->string('national_code')->nullable();
            $table->string('mobile')->nullable();
            $table->text('biography')->nullable();
            $table->integer('point')->default(0);
            $table->tinyInteger('level')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('email')->unique();
            $table->boolean('is_private')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('bg_photo_path', 2048)->nullable();
            $table->boolean('is_report')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
