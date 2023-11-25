<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('schedules', function (Blueprint $table) {
      $table->id();
      $table->timestamp('date');
      $table->string('shift', 50);
      $table->string('state', 30);
      $table->unsignedBigInteger('door_id');
      $table->timestamps();

      $table->foreign('door_id')->references('id')->on('doors');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('schedules', function (Blueprint $table) {
      $table->dropForeign(['door_id']);
    });
    Schema::dropIfExists('schedules');
  }
};
