<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $user  = Role::firstOrCreate(['name' => 'User']);

        $p1 = Permission::firstOrCreate(['name' => 'view_admin_data']);
        $p2 = Permission::firstOrCreate(['name' => 'view_user_data']);

        $admin->syncPermissions([$p1, $p2]);
        $user->syncPermissions([$p2]);
    }
}
