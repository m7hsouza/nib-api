<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
      'password' => Hash::make('nib@123456')
    ]);
  }
}
