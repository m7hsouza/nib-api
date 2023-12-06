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
    Schema::create('videos', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('description');
      $table->unsignedInteger('likes')->default(0);
      $table->unsignedBigInteger('user_id');
      $table->string('video_filename');
      $table->string('thumbnail_filename');
      $table->softDeletes();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('videos', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
    });
    Schema::dropIfExists('videos');
  }
};
