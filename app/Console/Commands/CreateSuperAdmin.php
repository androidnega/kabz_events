<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin 
                            {--email=superadmin@kabzsevent.com : The super admin email}
                            {--password=SuperAdmin123 : The super admin password}
                            {--name=Super Admin : The super admin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Super Admin user for KABZS EVENT';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🇬🇭 KABZS EVENT - Super Admin Setup');
        $this->info('=====================================');
        $this->newLine();

        // Get options
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // 1. Create super_admin role
        $this->info('1️⃣  Creating super_admin role...');
        $role = Role::firstOrCreate(['name' => 'super_admin']);
        
        if ($role->wasRecentlyCreated) {
            $this->info('   ✅ Super Admin role created!');
        } else {
            $this->info('   ℹ️  Super Admin role already exists.');
        }
        $this->newLine();

        // 2. Check if user exists
        $this->info('2️⃣  Creating Super Admin user...');
        
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            $this->warn("   ⚠️  User with email '{$email}' already exists!");
            $this->info('   Assigning super_admin role...');
            
            if (!$existingUser->hasRole('super_admin')) {
                $existingUser->assignRole('super_admin');
                $this->info('   ✅ Super Admin role assigned!');
            } else {
                $this->info('   ℹ️  User already has super_admin role.');
            }
            
            $user = $existingUser;
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            
            $user->assignRole('super_admin');
            $this->info('   ✅ Super Admin user created!');
        }
        
        $this->newLine();
        
        // 3. Display credentials
        $this->info('✅ SUCCESS! Super Admin Ready:');
        $this->info('=====================================');
        $this->line("👤 Name:     {$user->name}");
        $this->line("📧 Email:    {$user->email}");
        
        if (!$existingUser) {
            $this->line("🔑 Password: {$password}");
        } else {
            $this->line("🔑 Password: (unchanged - use existing password)");
        }
        
        $this->line("🎭 Role:     super_admin");
        $this->newLine();
        $this->line("🔗 Dashboard: http://localhost:8000/super-admin/dashboard");
        $this->info('=====================================');
        $this->newLine();
        $this->info('🎉 You can now log in with these credentials!');
        
        return self::SUCCESS;
    }
}
