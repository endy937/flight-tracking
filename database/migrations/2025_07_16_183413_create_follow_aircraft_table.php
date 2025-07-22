<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('follow_aircraft', function (Blueprint $table) {
            $table->id();
            $table->string('created_by')->nullable();
            $table->string('callsign');
            $table->decimal('lat', 10, 6);
            $table->decimal('lon', 10, 6);
            $table->string('registration')->nullable();
            $table->string('icao24bit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_aircraft');
    }
};
