<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoffeeDropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coffee_drops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('postcode');
            $table->string('open_Monday')->default('');
            $table->string('open_Tuesday')->default('');
            $table->string('open_Wednesday')->default('');
            $table->string('open_Thursday')->default('');
            $table->string('open_Friday')->default('');
            $table->string('open_Saturday')->default('');
            $table->string('open_Sunday')->default('');
            $table->string('closed_Monday')->default('');
            $table->string('closed_Tuesday')->default('');
            $table->string('closed_Wednesday')->default('');
            $table->string('closed_Thursday')->default('');
            $table->string('closed_Friday')->default('');
            $table->string('closed_Saturday')->default('');
            $table->string('closed_Sunday')->default('');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
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
        Schema::dropIfExists('coffee_drops');
    }
}
