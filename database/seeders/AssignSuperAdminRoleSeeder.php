<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class AssignSuperAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create the Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Get all permissions
        $allPermissions = Permission::all();

        // Assign all permissions to Super Admin role
        $superAdminRole->givePermissionTo($allPermissions);

        // Find the admin user
        $adminUser = User::where('email', 'admin@example.com')->first();

        if ($adminUser) {
            // Assign Super Admin role to the admin user
            $adminUser->assignRole($superAdminRole);
            
            $this->command->info('Super Admin role and all permissions assigned to admin@example.com successfully!');
        } else {
            $this->command->error('Admin user with email admin@example.com not found!');
        }
    }
}
