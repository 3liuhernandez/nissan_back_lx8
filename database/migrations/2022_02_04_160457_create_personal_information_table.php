<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_information', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->text('adress')->nullable();
            $table->string('aerial_aeroline', 100)->nullable();
            $table->string('aerial_arrive_time', 50)->nullable();
            $table->string('aerial_booking', 50)->nullable();
            $table->string('aerial_departure_time', 20)->nullable();
            $table->string('aerial_destination', 100)->nullable();
            $table->string('aerial_flight', 50)->nullable();
            $table->string('birthdate', 20)->nullable();
            $table->string('bus_arrive_time', 20)->nullable();
            $table->string('bus_booking', 20)->nullable();
            $table->string('bus_departure_time', 20)->nullable();
            $table->string('document', 100)->nullable();
            $table->string('food', 50)->nullable();
            $table->string('parking_car_model', 100)->nullable();
            $table->string('parking_patent', 100)->nullable();
            $table->string('size', 10)->nullable();
            $table->string('tel', 30)->nullable();
            $table->string('transport', 50)->nullable();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->tinyInteger('registered')->default(0);

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
        Schema::dropIfExists('personal_information');
    }
}
