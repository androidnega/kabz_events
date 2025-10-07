<?php

// KABZS EVENT - Create Admin User Script
// Run this with: php create-admin.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "========================================\n";
echo "   Creating Admin User for KABZS EVENT\n";
echo "========================================\n\n";

// Check if admin already exists
$existingAdmin = User::where('email', 'admin@kabzsevent.com')->first();

if ($existingAdmin) {
    echo "❌ Admin user already exists!\n";
    echo "Email: admin@kabzsevent.com\n\n";
    echo "To reset password, delete the user first or use a different email.\n";
    exit(1);
}

// Create admin user
try {
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@kabzsevent.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => now(),
    ]);
    
    // Assign admin role
    $user->assignRole('admin');
    
    echo "✓ Admin user created successfully!\n\n";
    echo "Login Credentials:\n";
    echo "==================\n";
    echo "Email:    admin@kabzsevent.com\n";
    echo "Password: password123\n\n";
    echo "Login URL: http://localhost:8000/login\n\n";
    echo "========================================\n";
    echo "   Admin user is ready to use!\n";
    echo "========================================\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating admin user:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

