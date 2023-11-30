<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->string('enrollment_number', length: 6)->unique();
      $table->string('avatar_url')->nullable();
      $table->string('phone', 20)->nullable();
      $table->enum('gender', ['male', 'female'])->default('male');
      $table->string('password');
      $table->string('state', 20)->default('active');
      $table->boolean('password_change_required')->default(true);
      $table->boolean('is_already_baptized')->default(false);
      $table->boolean('already_accepted_term')->default(false);
      $table->rememberToken();
      $table->timestamp('birth');
      $table->timestamp('email_verified_at')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};
