<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixtureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixture', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("week",2);
            $table->integer("home_team_id");
            $table->integer("away_team_id");
            $table->tinyInteger("home_team_score");
            $table->tinyInteger("away_team_score");
            $table->tinyInteger("played")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixture');
    }
}
