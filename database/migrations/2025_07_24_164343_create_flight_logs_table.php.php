<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flight_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_id')->unique();
            $table->string('tanggal');
            $table->timestamp('timestamp');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('flight_logs');
    }
};
