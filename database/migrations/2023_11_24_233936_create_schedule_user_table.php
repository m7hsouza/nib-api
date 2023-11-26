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
    Schema::create('schedule_user', function (Blueprint $table) {
      $table->unsignedBigInteger('leader_id');
      $table->unsignedBigInteger('schedule_id');

      $table->foreign('leader_id')->references('id')->on('users');
      $table->foreign('schedule_id')->references('id')->on('schedules');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('schedule_user', function (Blueprint $table) {
      $table->dropForeign(['leader_id']);
      $table->dropForeign(['schedule_id']);
    });
    Schema::dropIfExists('schedule_user');
  }
};
