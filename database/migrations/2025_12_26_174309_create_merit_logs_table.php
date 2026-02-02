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
        Schema::create('merit_logs', function (Blueprint $table) {
    $table->id('m_id');
    $table->foreignId('s_id')->constrained('students', 's_id')->onDelete('cascade');
    $table->foreignId('e_id')->constrained('events', 'e_id')->onDelete('cascade');
    $table->integer('points_added');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merit_logs');
    }
};
