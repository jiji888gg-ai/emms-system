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
       Schema::create('events', function (Blueprint $table) {
    $table->id('e_id');
    $table->foreignId('o_id')->constrained('organizers', 'o_id')->onDelete('cascade');
    $table->string('title');
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->decimal('location_lat', 10, 7);
    $table->decimal('location_long', 10, 7);
    $table->integer('radius_meter');
    $table->string('qr_code_token');
    $table->integer('merit_value');
    $table->string('status')->default('pending');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
