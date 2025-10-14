<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creating login credentials for testing...\n";

        // Create roles if they don't exist
        $roles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'vendor' => 'Vendor',
            'client' => 'Client',
            'user' => 'User'
        ];

        foreach ($roles as $roleName => $roleDisplay) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@kabzsevent.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        echo "Admin user: {$admin->email}\n";

        // Vendor User
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@kabzsevent.com'],
            [
                'name' => 'Vendor User',
                'password' => Hash::make('Vendor123!'),
                'email_verified_at' => now(),
            ]
        );
        $vendor->assignRole('vendor');
        echo "Vendor user: {$vendor->email}\n";

        // Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@kabzsevent.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');
        echo "Super Admin user: {$superAdmin->email}\n";

        // Test User
        $test = User::firstOrCreate(
            ['email' => 'test@kabzsevent.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('Test123!'),
                'email_verified_at' => now(),
            ]
        );
        $test->assignRole('user');
        echo "Test user: {$test->email}\n";

        echo "\nAll login credentials created successfully!\n";
        echo "Check LOGIN_CREDENTIALS.txt file for details.\n";
    }
}
