<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreMapMapPolygonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_map__map_polygons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('map_id');
            $table->timestamps();

            $table->foreign('map_id')->references('id')->on('netcore_map__maps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_map__map_polygons');
    }
}
