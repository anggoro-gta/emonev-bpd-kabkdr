<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
            ],[
                'name' => 'OPD',
                'guard_name' => 'web',
                'created_at' => now(),
            ]
        ];
        DB::table('roles')->insert($data);
    }
}
