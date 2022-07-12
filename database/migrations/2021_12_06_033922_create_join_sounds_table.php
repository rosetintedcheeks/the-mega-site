<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJoinSoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_sounds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('location', 512);
            $table->string('url', 512);
            $table->boolean('checked')->default(true);
            $table->string('discord_id', 255);
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
        Schema::dropIfExists('join_sounds');
    }
}
