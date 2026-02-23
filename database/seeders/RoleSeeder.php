<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Content Manager',
            'Marketing Manager',
            'Viewer',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
