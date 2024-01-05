<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users');
            $table->string('status');
            $table->string('departure_location');
            $table->string('departure_location_precise')->nullable();
            $table->string('destination');
            $table->string('destination_precise')->nullable();
            $table->string('route')->nullable();
            $table->string('phone_number');
            $table->dateTime('departure_time');
            $table->integer('available_seats');
            $table->decimal('price_per_passenger', 10, 2);
            $table->boolean('is_return_trip')->default(false);
            $table->string('return_departure_location')->nullable();
            $table->string('return_departure_location_precise')->nullable();
            $table->string('return_destination')->nullable();
            $table->string('return_destination_precise')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trips');
    }
}
