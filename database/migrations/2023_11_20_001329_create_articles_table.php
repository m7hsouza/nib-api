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
    Schema::create('articles', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->longText('content');
      $table->unsignedBigInteger('author_id');
      $table->boolean('is_highlighted')->default(false);
      $table->string('image_url');
      $table->softDeletes();
      $table->timestamps();

      $table->foreign('author_id')->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('articles', function (Blueprint $table) {
      $table->dropForeign(['author_id']);
    });
    Schema::dropIfExists('articles');
  }
};
