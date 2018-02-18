<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreMapMapMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_map__map_markers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('map_id');
            $table->unsignedInteger('map_polygon_id')->nullable();

            $table->decimal('latitude', 8, 6);
            $table->decimal('longitude', 9, 6);
            $table->string('address')->nullable();

            $table->timestamps();

            $table->foreign('map_id')->references('id')->on('netcore_map__maps')->onDelete('cascade');
            $table->foreign('map_polygon_id')->references('id')->on('netcore_map__map_polygons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_map__map_markers');
    }
}
