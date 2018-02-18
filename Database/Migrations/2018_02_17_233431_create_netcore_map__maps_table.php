<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreMapMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_map__maps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier')->unique()->index();

            // Center.
            $table->decimal('latitude', 8, 6);
            $table->decimal('longitude', 9, 6);
            $table->unsignedInteger('zoom')->default(10);

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
        Schema::dropIfExists('netcore_map__maps');
    }
}
