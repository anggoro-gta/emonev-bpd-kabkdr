<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'setting.user.create',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.user.read',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.user.update',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.user.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.role.create',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.role.read',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.role.update',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.role.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.permission.create',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.permission.read',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.permission.update',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'setting.permission.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
            ],
        ];
        DB::table('permissions')->insert($data);
        // assign Setting Permissions to Admin

        $r = Role::first(); //Match input role to db record
        $r->givePermissionTo(Permission::get());

        // assign Role Admin
        $user = User::first();
        $user->assignRole(1);
    }
}
