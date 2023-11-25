<?php

namespace Database\Seeders;

use App\Models\Door;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoorTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Door::firstOrCreate(['name' => '1A']);
    Door::firstOrCreate(['name' => '2B']);
    Door::firstOrCreate(['name' => '3C']);
    Door::firstOrCreate(['name' => '4D']);
  }
}
