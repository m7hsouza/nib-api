<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class RoleAndPermissionTablesSeeder extends Seeder
{

  public function run(): void
  {
    Permission::firstOrCreate(['name' => 'article.update']);
    Permission::firstOrCreate(['name' => 'article.create']);
    Permission::firstOrCreate(['name' => 'article.read']);
    Permission::firstOrCreate(['name' => 'article.delete']);

    Permission::firstOrCreate(['name' => 'user.update']);
    Permission::firstOrCreate(['name' => 'user.create']);
    Permission::firstOrCreate(['name' => 'user.read']);
    Permission::firstOrCreate(['name' => 'user.delete']);

    Permission::firstOrCreate(['name' => 'schedule.update']);
    Permission::firstOrCreate(['name' => 'schedule.create']);
    Permission::firstOrCreate(['name' => 'schedule.read']);
    Permission::firstOrCreate(['name' => 'schedule.all']);
    Permission::firstOrCreate(['name' => 'schedule.delete']);

    Role::firstOrCreate(["name" => "admin"])->syncPermissions([
      'article.update', 'article.create', 'article.read', 'article.delete',
      'user.update', 'user.create', 'user.read', 'user.delete',
      'schedule.update', 'schedule.create', 'schedule.read', 'schedule.delete', 'schedule.all',
    ]);

    Role::firstOrCreate(["name" => "writer"])->syncPermissions([
      'article.update', 'article.create', 'article.read', 'article.delete'
    ]);

    Role::firstOrCreate(["name" => "user"])->syncPermissions([
      'article.read'
    ]);

    User::firstWhere('enrollment_number', '000000')->assignRole('admin');
  }
}
