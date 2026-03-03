<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in the correct order
        
        // 1. Basic system seeders
        $this->call([
            AdminUserSeeder::class,
            AppSettingSeeder::class,
            CreateSuperAdminRoleSeeder::class,
            RoleSeeder::class,
        ]);
        
        // 2. Multilingual seeders
        $this->call([
            LocaleSeeder::class,
        ]);
        
        // 3. Permission seeders
        $this->call([
            ComprehensivePermissionsSeeder::class,
            CertificationPermissionsSeeder::class,
            TestimonialPermissionsSeeder::class,
        ]);
        
        // 4. Assign Super Admin role to admin user
        $this->call(AssignSuperAdminRoleSeeder::class);
        
        // 5. Test user (optional)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        
        $this->command->info('All seeders executed successfully!');
    }
}
