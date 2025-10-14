<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Fixing user passwords...\n";

        // Fix Admin User
        $admin = User::where('email', 'admin@kabzsevent.com')->first();
        if ($admin) {
            $admin->password = Hash::make('password123');
            $admin->save();
            echo "Admin password updated: admin@kabzsevent.com / password123\n";
        }

        // Fix Vendor User
        $vendor = User::where('email', 'vendor@kabzsevent.com')->first();
        if ($vendor) {
            $vendor->password = Hash::make('password123');
            $vendor->save();
            echo "Vendor password updated: vendor@kabzsevent.com / password123\n";
        }

        // Fix Super Admin User
        $superAdmin = User::where('email', 'superadmin@kabzsevent.com')->first();
        if ($superAdmin) {
            $superAdmin->password = Hash::make('password123');
            $superAdmin->save();
            echo "Super Admin password updated: superadmin@kabzsevent.com / password123\n";
        }

        // Fix Test User
        $test = User::where('email', 'test@kabzsevent.com')->first();
        if ($test) {
            $test->password = Hash::make('password123');
            $test->save();
            echo "Test password updated: test@kabzsevent.com / password123\n";
        }

        echo "\nAll passwords updated to 'password123'!\n";
    }
}
