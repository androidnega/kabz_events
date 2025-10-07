<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seed roles and permissions for KABZS EVENT system.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Vendor Management
            'view vendors',
            'create vendor',
            'edit vendor',
            'delete vendors',
            'verify vendors',
            'approve vendors',
            'reject vendors',
            
            // Service Management
            'view services',
            'create services',
            'edit services',
            'delete services',
            
            // Category Management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Review Management
            'view reviews',
            'create review',
            'edit review',
            'delete review',
            'moderate reviews',
            
            // Featured Ads
            'view ads',
            'create ads',
            'edit ads',
            'delete ads',
            'manage featured ads',
            
            // Bookmarks
            'bookmark vendors',
            'view bookmarks',
            
            // Contact & Inquiries
            'contact vendors',
            'view inquiries',
            
            // Subscription
            'manage subscriptions',
            'view subscription',
            
            // Reports & Analytics
            'view reports',
            'view analytics',
            
            // System
            'access admin panel',
            'access vendor dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $vendorRole = Role::create(['name' => 'vendor']);
        $clientRole = Role::create(['name' => 'client']);

        // Assign All Permissions to Admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign Vendor Permissions
        $vendorRole->givePermissionTo([
            'view vendors',
            'create vendor',
            'edit vendor',
            'view services',
            'create services',
            'edit services',
            'delete services',
            'view reviews',
            'view inquiries',
            'view subscription',
            'access vendor dashboard',
        ]);

        // Assign Client Permissions
        $clientRole->givePermissionTo([
            'view vendors',
            'view services',
            'view reviews',
            'create review',
            'edit review',
            'delete review',
            'bookmark vendors',
            'view bookmarks',
            'contact vendors',
        ]);

        $this->command->info('Roles and permissions seeded successfully for KABZS EVENT!');
        $this->command->info('Created roles: admin, vendor, client');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}

