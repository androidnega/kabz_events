<?php

/**
 * KABZS EVENT - Create Super Admin User
 * 
 * This script creates a super_admin role and assigns it to a user
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "🇬🇭 KABZS EVENT - Super Admin Setup\n";
echo "=====================================\n\n";

try {
    // 1. Create super_admin role if it doesn't exist
    echo "1️⃣  Checking for super_admin role...\n";
    
    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
    
    if ($superAdminRole->wasRecentlyCreated) {
        echo "   ✅ Super Admin role created successfully!\n\n";
    } else {
        echo "   ℹ️  Super Admin role already exists.\n\n";
    }

    // 2. Check if super admin user exists
    echo "2️⃣  Checking for existing super admin user...\n";
    
    $existingSuperAdmin = User::role('super_admin')->first();
    
    if ($existingSuperAdmin) {
        echo "   ℹ️  Super Admin already exists:\n";
        echo "   📧 Email: {$existingSuperAdmin->email}\n";
        echo "   👤 Name: {$existingSuperAdmin->name}\n\n";
        
        echo "3️⃣  Do you want to create another super admin? (yes/no): ";
        $answer = trim(fgets(STDIN));
        
        if (strtolower($answer) !== 'yes' && strtolower($answer) !== 'y') {
            echo "\n✅ Setup complete! Use existing super admin credentials.\n";
            echo "\n🔗 Dashboard: http://localhost:8000/super-admin/dashboard\n";
            exit(0);
        }
    }

    // 3. Create new super admin user
    echo "\n3️⃣  Creating new Super Admin user...\n";
    echo "   Enter name (default: Super Admin): ";
    $name = trim(fgets(STDIN));
    $name = empty($name) ? 'Super Admin' : $name;

    echo "   Enter email (default: superadmin@kabzsevent.com): ";
    $email = trim(fgets(STDIN));
    $email = empty($email) ? 'superadmin@kabzsevent.com' : $email;

    // Check if email already exists
    if (User::where('email', $email)->exists()) {
        echo "\n   ❌ Error: User with email '{$email}' already exists!\n";
        echo "   Assigning super_admin role to existing user...\n";
        
        $user = User::where('email', $email)->first();
        $user->assignRole('super_admin');
        
        echo "\n   ✅ Super Admin role assigned to: {$user->name}\n";
        echo "   📧 Email: {$email}\n";
        echo "\n🔗 Dashboard: http://localhost:8000/super-admin/dashboard\n";
        exit(0);
    }

    echo "   Enter password (default: password123): ";
    $password = trim(fgets(STDIN));
    $password = empty($password) ? 'password123' : $password;

    // Create the user
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);

    // Assign super_admin role
    $user->assignRole('super_admin');

    echo "\n✅ SUCCESS! Super Admin created:\n";
    echo "=====================================\n";
    echo "👤 Name:     {$name}\n";
    echo "📧 Email:    {$email}\n";
    echo "🔑 Password: {$password}\n";
    echo "🎭 Role:     super_admin\n";
    echo "\n🔗 Dashboard: http://localhost:8000/super-admin/dashboard\n";
    echo "=====================================\n";
    echo "\n🎉 You can now log in with these credentials!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nTry running: php artisan migrate:fresh --seed\n";
    exit(1);
}

