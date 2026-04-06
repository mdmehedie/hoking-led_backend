<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $contentManager = Role::firstOrCreate(['name' => 'Content Manager']);
        $marketingManager = Role::firstOrCreate(['name' => 'Marketing Manager']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // Define resource permissions
        $resources = [
            'user',
            'role',
            'blog',
            'casestudy',
            'news',
            'page',
            'author',
            'product',
            'testimonial',
            'certificationaward',
            'category',
            'content',
            'coreadvantage',
            'featuredproduct',
            'form',
            'lead',
            'slider',
            'appsetting',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        // Super Admin gets all permissions
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permission = Permission::where('name', $action . ' ' . $resource)->first();
                if ($permission) {
                    $superAdmin->givePermissionTo($permission);
                }
            }
        }

        // Add special permissions for Super Admin
        $specialPermissions = [
            'publish blog',
            'publish casestudy',
            'publish news',
            'publish page',
            'publish product',
            'feature product',
            'manage settings',
            'manage users',
            'manage roles',
            'manage multilingual',
            'manage socialmedia',
            'manage webhooks',
        ];

        foreach ($specialPermissions as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission) {
                $superAdmin->givePermissionTo($permission);
            }
        }

        // Admin gets most permissions except user and role management
        $adminResources = [
            'blog',
            'casestudy',
            'news',
            'page',
            'author',
            'product',
            'testimonial',
            'certificationaward',
            'category',
            'content',
            'coreadvantage',
            'featuredproduct',
            'form',
            'lead',
            'slider',
            'appsetting',
        ];

        foreach ($adminResources as $resource) {
            foreach ($actions as $action) {
                $permission = Permission::where('name', $action . ' ' . $resource)->first();
                if ($permission) {
                    $admin->givePermissionTo($permission);
                }
            }
        }

        // Add publishing permissions for Admin
        $adminPublishing = [
            'publish blog',
            'publish casestudy',
            'publish news',
            'publish page',
            'publish product',
            'feature product',
            'manage socialmedia',
            'manage webhooks',
        ];

        foreach ($adminPublishing as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission) {
                $admin->givePermissionTo($permission);
            }
        }

        // Content Manager gets content-related permissions
        $contentResources = [
            'blog',
            'casestudy',
            'news',
            'page',
            'author',
            'testimonial',
            'content',
            'category',
        ];

        foreach ($contentResources as $resource) {
            foreach (['view', 'create', 'edit'] as $action) {
                $permission = Permission::where('name', $action . ' ' . $resource)->first();
                if ($permission) {
                    $contentManager->givePermissionTo($permission);
                }
            }
        }

        // Add publishing permissions for Content Manager
        $contentPublishing = [
            'publish blog',
            'publish casestudy',
            'publish news',
            'publish page',
        ];

        foreach ($contentPublishing as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission) {
                $contentManager->givePermissionTo($permission);
            }
        }

        // Marketing Manager gets marketing-related permissions
        $marketingResources = [
            'product',
            'featuredproduct',
            'slider',
            'lead',
            'form',
        ];

        foreach ($marketingResources as $resource) {
            foreach (['view', 'create', 'edit'] as $action) {
                $permission = Permission::where('name', $action . ' ' . $resource)->first();
                if ($permission) {
                    $marketingManager->givePermissionTo($permission);
                }
            }
        }

        // Add marketing-specific permissions
        $marketingSpecific = [
            'publish product',
            'feature product',
            'manage socialmedia',
        ];

        foreach ($marketingSpecific as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission) {
                $marketingManager->givePermissionTo($permission);
            }
        }

        // Viewer gets only view permissions for all resources
        foreach ($resources as $resource) {
            $permission = Permission::where('name', 'view ' . $resource)->first();
            if ($permission) {
                $viewer->givePermissionTo($permission);
            }
        }

        // Add view permissions for custom pages
        $viewerCustom = [
            'view dashboard',
        ];

        foreach ($viewerCustom as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission) {
                $viewer->givePermissionTo($permission);
            }
        }

        $this->command->info('Roles and permissions assigned successfully!');
    }
}
