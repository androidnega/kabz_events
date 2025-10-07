# 🚀 KABZS EVENT - Quick Installation Commands

## Copy-Paste Command Reference

This document provides ready-to-use commands for setting up KABZS EVENT.

---

## 📋 Prerequisites Check

```bash
# Check Docker version
docker --version

# Check Docker Compose version
docker-compose --version

# Check Node.js version
node --version

# Check NPM version
npm --version

# Check Git version
git --version
```

---

## 🎯 Method 1: Laravel Sail Installation (Recommended)

### Step 1: Create Project with Sail
```bash
curl -s "https://laravel.build/kabzs-event?with=mysql,redis,mailpit" | bash
```

### Step 2: Navigate to Project
```bash
cd kabzs-event
```

### Step 3: Start Docker Containers
```bash
./vendor/bin/sail up -d
```

### Step 4: Install Laravel Breeze
```bash
./vendor/bin/sail composer require laravel/breeze --dev
./vendor/bin/sail artisan breeze:install blade
```

### Step 5: Install Spatie Packages
```bash
# Laravel Permission
./vendor/bin/sail composer require spatie/laravel-permission

# Media Library
./vendor/bin/sail composer require spatie/laravel-medialibrary
```

### Step 6: Publish Package Configurations
```bash
# Publish Spatie Permission
./vendor/bin/sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Publish Media Library
./vendor/bin/sail artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

### Step 7: Update Environment File
```bash
# The .env file should already exist, update these values:
# DB_DATABASE=event_management_db
# APP_NAME="KABZS EVENT"
```

Or use this command to update the database name:
```bash
./vendor/bin/sail artisan config:clear
# Manually edit .env file to set DB_DATABASE=event_management_db
```

### Step 8: Run Migrations
```bash
./vendor/bin/sail artisan migrate
```

### Step 9: Seed Roles and Permissions
```bash
./vendor/bin/sail artisan db:seed --class=RoleSeeder
```

### Step 10: Install Frontend Dependencies
```bash
npm install
npm run dev
```

### Step 11: Open in Browser
```
http://localhost
```

---

## 🎯 Method 2: Traditional Composer Installation

### Step 1: Create Laravel Project
```bash
composer create-project laravel/laravel:^10.0 kabzs-event
cd kabzs-event
```

### Step 2: Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### Step 3: Install Spatie Packages
```bash
composer require spatie/laravel-permission
composer require spatie/laravel-medialibrary
```

### Step 4: Publish Configurations
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

### Step 5: Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:
```env
APP_NAME="KABZS EVENT"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_management_db
DB_USERNAME=root
DB_PASSWORD=
```

### Step 6: Create Database
```bash
# Access MySQL
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE event_management_db;
exit;
```

### Step 7: Run Migrations
```bash
php artisan migrate
```

### Step 8: Seed Database
```bash
php artisan db:seed --class=RoleSeeder
```

### Step 9: Install Frontend
```bash
npm install
npm run dev
```

### Step 10: Start Development Server
```bash
php artisan serve
```

### Step 11: Open in Browser
```
http://localhost:8000
```

---

## 🛠️ Useful Sail Aliases

### For Linux/Mac (Add to .bashrc or .zshrc)
```bash
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

### For Windows PowerShell (Add to Profile)
```powershell
# Open PowerShell profile
notepad $PROFILE

# Add this line:
function sail { ./vendor/bin/sail.ps1 $args }

# Reload profile
. $PROFILE
```

After setting up the alias, you can use:
```bash
sail up -d
sail artisan migrate
sail composer install
```

---

## 🔧 Post-Installation Commands

### Clear All Cache
```bash
sail artisan optimize:clear
# or
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear
```

### View Permissions
```bash
sail artisan permission:show
```

### Create Admin User (Via Tinker)
```bash
sail artisan tinker

# In Tinker:
$user = User::create([
    'name' => 'Admin User',
    'email' => 'admin@kabzsevent.com',
    'password' => bcrypt('password123'),
]);
$user->assignRole('admin');
exit
```

### Check Migration Status
```bash
sail artisan migrate:status
```

### Rollback Migrations (If Needed)
```bash
sail artisan migrate:rollback
```

### Fresh Migration with Seed
```bash
sail artisan migrate:fresh --seed
```

---

## 📦 Package Version Check

```bash
sail composer show laravel/framework
sail composer show spatie/laravel-permission
sail composer show spatie/laravel-medialibrary
sail composer show laravel/breeze
```

---

## 🧪 Testing Installation

### Test Database Connection
```bash
sail artisan migrate:status
```

### Test Redis Connection
```bash
sail artisan tinker

# In Tinker:
Cache::put('test', 'Redis working!', 60);
Cache::get('test');
exit
```

### View Application Routes
```bash
sail artisan route:list
```

### Run Tests (After Setup)
```bash
sail artisan test
```

---

## 🐛 Common Issues & Fixes

### Port 80 Already in Use
```bash
# Stop Sail
sail down

# Edit docker-compose.yml to use different port
# Change "80:80" to "8080:80"

# Restart
sail up -d

# Access at http://localhost:8080
```

### Permission Denied Errors (Linux/Mac)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Node Module Issues
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Database Connection Issues
```bash
# Check if MySQL container is running
sail ps

# Restart MySQL
sail down
sail up -d

# Check logs
sail logs mysql
```

### Composer Memory Limit
```bash
COMPOSER_MEMORY_LIMIT=-1 sail composer install
```

---

## 📊 Verify Installation Checklist

Run these commands to verify everything is working:

```bash
# 1. Check Sail is running
sail ps

# 2. Check Laravel version
sail artisan --version

# 3. Check database
sail artisan migrate:status

# 4. Check roles
sail artisan permission:show

# 5. Check routes
sail artisan route:list | grep login

# 6. Test Redis
sail artisan tinker --execute="Cache::put('test', 'works'); echo Cache::get('test');"

# 7. Check installed packages
sail composer show | grep spatie
```

Expected output:
- ✅ All containers running
- ✅ Laravel 10.x displayed
- ✅ Migrations ran successfully
- ✅ Roles: admin, vendor, client visible
- ✅ Auth routes present
- ✅ Redis returns "works"
- ✅ Spatie packages listed

---

## 🎯 Next Steps After Installation

1. **Access the application**: http://localhost
2. **Register a test user**
3. **Assign roles via Tinker**
4. **Start building features** (vendors, categories, etc.)
5. **Review SETUP.md** for detailed architecture info

---

## 💡 Development Workflow

```bash
# Morning startup
sail up -d

# Make changes to code
# Blade files auto-reload
# For CSS/JS changes:
npm run dev

# Create migration
sail artisan make:migration create_vendors_table

# Run migration
sail artisan migrate

# Create model
sail artisan make:model Vendor -mcr

# Evening shutdown
sail down
```

---

## 📞 Need Help?

- Check **SETUP.md** for detailed explanations
- Review **README.md** for project overview
- Laravel Docs: https://laravel.com/docs/10.x
- Sail Docs: https://laravel.com/docs/10.x/sail

---

**🎉 Happy Coding! Welcome to KABZS EVENT Development.**

