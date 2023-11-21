<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class RoleAndPermissionTablesSeeder extends Seeder
{

  public function run(): void
  {
    Permission::firstOrCreate(['name' => 'update article']);
    Permission::firstOrCreate(['name' => 'create article']);
    Permission::firstOrCreate(['name' => 'read article']);
    Permission::firstOrCreate(['name' => 'delete article']);

    Permission::firstOrCreate(['name' => 'update user']);
    Permission::firstOrCreate(['name' => 'create user']);
    Permission::firstOrCreate(['name' => 'read user']);
    Permission::firstOrCreate(['name' => 'delete user']);

    Role::firstOrCreate(["name" => "admin"])->syncPermissions([
      'update article', 'create article', 'read article', 'delete article',
      'update user', 'create user', 'read user'
    ]);

    Role::firstOrCreate(["name" => "writer"])->syncPermissions([
      'update article', 'create article', 'read article', 'delete article'
    ]);

    Role::firstOrCreate(["name" => "user"])->syncPermissions([
      'read article'
    ]);

    User::firstWhere('enrollment_number', '000000')->assignRole('admin');
  }
}
