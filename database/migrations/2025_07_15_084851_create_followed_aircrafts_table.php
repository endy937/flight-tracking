<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
//     public function up()
// {
//     Schema::create('followed_aircrafts', function (Blueprint $table) {
//         $table->id();
//         $table->foreignId('user_id')->constrained()->onDelete('cascade');
//         $table->string('callsign');
//         $table->decimal('lat', 10, 6);
//         $table->decimal('lon', 10, 6);
//         $table->string('registration')->nullable();
//         $table->string('icao24bit')->nullable();
//         $table->timestamps();
//     });
// }
public function up()
{
    Schema::create('followed_aircrafts', function (Blueprint $table) {
        $table->id();
        $table->string('callsign');
        $table->decimal('lat', 10, 6);
        $table->decimal('lon', 10, 6);
        $table->string('registration')->nullable();
        $table->string('icao24bit')->nullable();
        $table->timestamps();
    });
}

};
