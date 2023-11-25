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
    Schema::create('schedules_users', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('leader_id');
      $table->unsignedBigInteger('schedule_id');
      $table->timestamps();

      $table->foreign('leader_id')->references('id')->on('users');
      $table->foreign('schedule_id')->references('id')->on('schedules');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('schedules_users', function (Blueprint $table) {
      $table->dropForeign(['leader_id']);
      $table->dropForeign(['schedule_id']);
    });
    Schema::dropIfExists('schedules_users');
  }
};
