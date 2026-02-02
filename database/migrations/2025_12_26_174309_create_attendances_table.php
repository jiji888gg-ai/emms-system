<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
    $table->id('att_id');
    $table->foreignId('s_id')->constrained('students', 's_id')->onDelete('cascade');
    $table->foreignId('e_id')->constrained('events', 'e_id')->onDelete('cascade');
    $table->dateTime('scan_time');
    $table->string('device_id');
    $table->string('status')->default('present');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
