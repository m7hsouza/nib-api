<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $users = User::create([
      'name' => 'Admin',
      'email' => 'admin@nib.com',
      'password' => 'nib@123456',
      'birth' => new \Datetime
    ]);
  }
}
