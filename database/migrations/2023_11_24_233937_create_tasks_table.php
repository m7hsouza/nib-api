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
    Schema::create('tasks', function (Blueprint $table) {
      $table->id();
      $table->string('description');
      $table->unsignedBigInteger('schedule_id');
      $table->unsignedBigInteger('responsible_id');
      $table->unsignedDouble('grade')->nullable();
      $table->string('observation')->nullable();
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('schedule_id')->references('id')->on('schedules');
      $table->foreign('responsible_id')->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('tasks', function (Blueprint $table) {
      $table->dropForeign(['schedule_id']);
      $table->dropForeign(['responsible_id']);
    });
    Schema::dropIfExists('tasks');
  }
};
