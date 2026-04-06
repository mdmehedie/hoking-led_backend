<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ComprehensivePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resource permissions
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
            'project',
            'featuredproduct',
            'form',
            'lead',
            'slider',
            'appsetting',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => $action . ' ' . $resource]);
            }
        }

        // Product-specific permissions
        Permission::firstOrCreate(['name' => 'publish product']);
        Permission::firstOrCreate(['name' => 'feature product']);

        // Content-specific permissions
        Permission::firstOrCreate(['name' => 'publish blog']);
        Permission::firstOrCreate(['name' => 'publish casestudy']);
        Permission::firstOrCreate(['name' => 'publish news']);
        Permission::firstOrCreate(['name' => 'publish page']);

        // Custom page permissions
        $customPermissions = [
            'view dashboard',
            'manage settings',
            'manage users',
            'manage roles',
            'manage multilingual',
            'manage socialmedia',
            'manage webhooks',
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Comprehensive permissions created successfully!');
    }
}
