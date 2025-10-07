# KABZS EVENT - Complete Setup Guide

## 🎯 Project Overview
**KABZS EVENT** is a scalable event and vendor management platform built with Laravel 10.

**Database Name:** `event_management_db`

---

## 📋 Prerequisites

Before starting, ensure you have:
- **Docker Desktop** installed and running
- **Git** installed
- **Composer** (optional, Docker will handle this)
- **Node.js & NPM** (v18+ recommended)

---

## 🚀 Step 1: Create Laravel 10 Project

### Option A: Using Laravel Sail (Recommended)
```bash
# This creates a new Laravel project with Sail
curl -s "https://laravel.build/kabzs-event?with=mysql,redis" | bash

# Move into the project directory
cd kabzs-event

# Start Docker containers
./vendor/bin/sail up -d
```

### Option B: Using Composer (If you have PHP 8.1+ locally)
```bash
# Create Laravel 10 project
composer create-project laravel/laravel:^10.0 kabzs-event

# Navigate to project
cd kabzs-event
```

---

## 🔧 Step 2: Configure Environment (.env)

Copy the example environment file:
```bash
cp .env.example .env
```

Update your `.env` file with these settings:

```env
APP_NAME="KABZS EVENT"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=event_management_db
DB_USERNAME=sail
DB_PASSWORD=password

# Redis Configuration
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration (Update for production)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@kabzsevent.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Generate application key:
```bash
# If using Sail
./vendor/bin/sail artisan key:generate

# If using local PHP
php artisan key:generate
```

---

## 📦 Step 3: Install Laravel Breeze (Authentication)

```bash
# Install Breeze package
composer require laravel/breeze --dev

# Install Breeze with Blade + Tailwind
php artisan breeze:install blade

# Or with Sail
./vendor/bin/sail composer require laravel/breeze --dev
./vendor/bin/sail artisan breeze:install blade

# Install Node dependencies and build assets
npm install
npm run dev
```

---

## 🔐 Step 4: Install Spatie Packages

### A. Laravel Permission (Roles & Permissions)
```bash
composer require spatie/laravel-permission

# Publish the migration and config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### B. Laravel Media Library
```bash
composer require spatie/laravel-medialibrary

# Publish the migration
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

**Sail commands:**
```bash
./vendor/bin/sail composer require spatie/laravel-permission
./vendor/bin/sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

./vendor/bin/sail composer require spatie/laravel-medialibrary
./vendor/bin/sail artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

---

## 🐳 Step 5: Docker Compose Configuration (Sail)

Laravel Sail provides Docker out-of-the-box. Your `docker-compose.yml` should include:
- **Nginx** (via Laravel Sail's PHP container)
- **PHP 8.2+**
- **MySQL 8.0**
- **Redis**
- **Mailpit** (for local email testing)

Start the containers:
```bash
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs
```

### Sail Alias (Optional but Recommended)
Add to your shell profile (`.bashrc`, `.zshrc`, or PowerShell profile):
```bash
alias sail='./vendor/bin/sail'
```

Then use:
```bash
sail up -d
sail artisan migrate
sail composer install
```

---

## 🗃️ Step 6: Database Setup

### Create the Database
```bash
# Access MySQL
./vendor/bin/sail mysql

# In MySQL prompt
CREATE DATABASE IF NOT EXISTS event_management_db;
exit;
```

### Run Migrations
```bash
# Run all migrations
./vendor/bin/sail artisan migrate

# Or with local PHP
php artisan migrate
```

---

## 👥 Step 7: Seed Default Roles

### Create Role Seeder
```bash
./vendor/bin/sail artisan make:seeder RoleSeeder
```

Update `database/seeders/RoleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'vendor']);
        Role::create(['name' => 'client']);

        // Create permissions (examples)
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage vendors']);
        Permission::create(['name' => 'verify vendors']);
        Permission::create(['name' => 'manage ads']);
        Permission::create(['name' => 'view vendors']);
        Permission::create(['name' => 'contact vendors']);
        Permission::create(['name' => 'leave reviews']);

        // Assign permissions to roles
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(Permission::all());

        $vendor = Role::findByName('vendor');
        $vendor->givePermissionTo(['view vendors', 'leave reviews']);

        $client = Role::findByName('client');
        $client->givePermissionTo(['view vendors', 'contact vendors', 'leave reviews']);
    }
}
```

### Run the Seeder
```bash
./vendor/bin/sail artisan db:seed --class=RoleSeeder
```

Or update `database/seeders/DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
    ]);
}
```

Then run:
```bash
./vendor/bin/sail artisan db:seed
```

---

## 📁 Expected Project Structure

```
kabzs-event/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── VendorController.php
│   │   │   ├── ClientController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vendor.php
│   │   ├── Category.php
│   │   ├── Service.php
│   │   ├── Review.php
│   │   └── ...
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RoleSeeder.php
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── vendors/
│   │   ├── admin/
│   │   └── layouts/
│   └── css/
├── routes/
│   ├── web.php
│   └── api.php
├── docker-compose.yml
├── .env
├── .env.example
└── README.md
```

---

## 🧪 Step 8: Verify Installation

```bash
# Check Laravel version
./vendor/bin/sail artisan --version

# Check installed packages
./vendor/bin/sail composer show

# Test database connection
./vendor/bin/sail artisan migrate:status

# Check roles
./vendor/bin/sail artisan permission:show
```

---

## 🎨 Step 9: Build Frontend Assets

```bash
# Install dependencies
npm install

# Development build with hot reload
npm run dev

# Production build
npm run build
```

---

## 🚀 Step 10: Access the Application

- **Application URL:** http://localhost
- **Mailpit (Email Testing):** http://localhost:8025
- **MySQL Port:** 3306
- **Redis Port:** 6379

---

## 📝 Next Development Phases

### Phase 1: Core Models & Migrations
- Users (with Breeze)
- Vendors
- Categories
- Services

### Phase 2: Vendor Features
- Registration & Verification
- Profile Management
- Service Listings

### Phase 3: Client Features
- Browse Vendors
- Reviews & Ratings
- Bookmarks

### Phase 4: Admin Features
- Vendor Verification
- Featured Ads Management
- User Management

### Phase 5: Advanced Features
- Subscriptions
- Search (Meilisearch/Algolia)
- Notifications
- Analytics

---

## 🛠️ Common Commands

```bash
# Start development server
sail up -d

# Run migrations
sail artisan migrate

# Create migration
sail artisan make:migration create_vendors_table

# Create model with migration
sail artisan make:model Vendor -m

# Create controller
sail artisan make:controller VendorController --resource

# Clear cache
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

# Run tests
sail artisan test

# Access Laravel Tinker
sail artisan tinker
```

---

## 🐛 Troubleshooting

### Port Already in Use
```bash
# Check what's using port 80
netstat -ano | findstr :80

# Stop Sail and use different ports
sail down
# Edit docker-compose.yml to change port mappings
```

### Permission Issues (Linux/Mac)
```bash
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

### Windows-Specific Issues
- Ensure WSL2 is enabled for Docker Desktop
- Run PowerShell as Administrator when needed
- Use forward slashes in paths when possible

---

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Laravel Sail](https://laravel.com/docs/10.x/sail)
- [Laravel Breeze](https://laravel.com/docs/10.x/starter-kits#breeze)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Tailwind CSS](https://tailwindcss.com/docs)

---

## ✅ Checklist

- [ ] Docker Desktop installed and running
- [ ] Laravel 10 project created
- [ ] `.env` configured with `event_management_db`
- [ ] Laravel Breeze installed (Blade + Tailwind)
- [ ] Spatie packages installed (permission, medialibrary)
- [ ] Docker Sail running
- [ ] Database migrated
- [ ] Roles seeded (admin, vendor, client)
- [ ] Frontend assets compiled
- [ ] Application accessible at http://localhost

---

**🎉 You're all set! Ready to build KABZS EVENT.**

