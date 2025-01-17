<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->string('photos')->nullable();
            $table->float('rating')->nullable();
            $table->float('user_rating')->nullable();
            $table->string('place_id')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('postal_code')->nullable();
            $table->float('rating_score')->nullable();
            $table->float('userRating_score')->nullable();
            $table->float('photo_score')->nullable();
            $table->float('website_score')->nullable();
            $table->float('avg_score')->nullable();
            $table->integer('rank')->nullable();

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
        Schema::dropIfExists('motels');
    }
}
