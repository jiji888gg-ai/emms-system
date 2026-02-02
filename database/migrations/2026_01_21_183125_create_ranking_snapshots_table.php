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
    Schema::create('ranking_snapshots', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('s_id');
        $table->integer('rank');
        $table->date('snapshot_date');
        $table->timestamps();

        // optional: foreign key
        // $table->foreign('s_id')->references('s_id')->on('students')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_snapshots');
    }
};
