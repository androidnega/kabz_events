# 🎉 KABZS EVENT - Complete Setup Instructions

## ✅ What Has Been Done

I've successfully set up the following for your KABZS EVENT project:

### ✓ Installed Components
1. **Composer** - PHP dependency manager (composer.phar)
2. **Laravel 10** - Full framework installation
3. **Laravel Breeze** - Authentication scaffolding (Blade + Tailwind CSS)
4. **Spatie Laravel Permission** - Role and permission management
5. **Spatie Laravel Media Library** - Media handling
6. **User Model** - Updated with HasRoles trait
7. **Environment Configuration** - .env file configured
8. **Application Key** - Generated and set
9. **Migrations** - All ready including permission tables

### ✓ Database Configuration
- **Database Name:** `event_management_db`
- **Connection:** MySQL (127.0.0.1:3306)
- **Username:** root
- **Password:** (needs to be set in .env)

---

## 🚀 Next Steps - COMPLETE THESE NOW

### Step 1: Start MySQL in XAMPP Control Panel ⚠️

1. Open **XAMPP Control Panel**
2. Click **Start** next to **MySQL**
3. Wait until it shows "Running" (green background)

![XAMPP MySQL](https://i.imgur.com/xampp-mysql.png)

---

### Step 2: Create the Database

**Option A: Using phpMyAdmin (Easiest)**

1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click on **"New"** in the left sidebar
3. Enter database name: `event_management_db`
4. Select collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

**Option B: Using MySQL Command Line**

```bash
# Open terminal and run:
C:\xampp\mysql\bin\mysql.exe -u root -p

# When prompted for password, press Enter if no password set
# Or enter your MySQL root password

# Then run:
CREATE DATABASE event_management_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES LIKE 'event_management_db';
exit;
```

**Option C: Use the SQL File**

1. Go to phpMyAdmin: http://localhost/phpmyadmin
2. Click on "SQL" tab
3. Copy the contents of `create-database.sql`
4. Click "Go"

---

### Step 3: Configure Database Password (If MySQL Has Password)

If your MySQL root user has a password:

**Edit `.env` file in the project root and update:**

```env
DB_PASSWORD=your_mysql_password_here
```

**To check if MySQL has a password:**
- Try logging into phpMyAdmin
- If it asks for password, update .env
- If it logs in automatically, password is empty (already configured)

---

### Step 4: Run Migrations

**Option A: Use the Batch Script (Recommended)**

Simply double-click: **`run-migrations.bat`**

This will:
- Run all database migrations
- Seed roles (admin, vendor, client)
- Install Node modules

**Option B: Manual Commands**

Open PowerShell in the project directory and run:

```bash
# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RoleSeeder

# Install Node modules
npm install
```

---

### Step 5: Verify Setup

Check if everything is working:

```bash
# Check migration status
php artisan migrate:status

# Should show all migrations as "Ran"

# Check roles
php artisan tinker
```

In Tinker, run:
```php
\Spatie\Permission\Models\Role::all(['name']);
// Should show: admin, vendor, client
exit
```

---

## 🎨 Step 6: Build Frontend Assets

Open a **new terminal** window and run:

```bash
npm run dev
```

**Keep this terminal window open!** It will watch for file changes and recompile assets.

---

## 🚀 Step 7: Start the Development Server

Open **another terminal** window and run:

```bash
php artisan serve
```

You should see:
```
Server running on [http://127.0.0.1:8000]
```

---

## 🌐 Step 8: Access Your Application

Open your browser and go to: **http://localhost:8000**

You should see the Laravel welcome page with authentication options!

**Available URLs:**
- Homepage: http://localhost:8000
- Register: http://localhost:8000/register
- Login: http://localhost:8000/login
- Dashboard: http://localhost:8000/dashboard (after login)

---

## 👤 Step 9: Create Your First Admin User

### Option A: Via Tinker (Recommended)

```bash
php artisan tinker
```

In Tinker, paste this:

```php
$user = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@kabzsevent.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);

$user->assignRole('admin');

echo "Admin created! Login with: admin@kabzsevent.com / password123\n";
exit
```

### Option B: Via Registration

1. Go to http://localhost:8000/register
2. Register a new account
3. Then assign admin role via Tinker:

```php
$user = \App\Models\User::where('email', 'your-email@example.com')->first();
$user->assignRole('admin');
```

---

## ✅ Verification Checklist

- [ ] MySQL is running in XAMPP
- [ ] Database `event_management_db` exists
- [ ] Migrations completed successfully
- [ ] Roles seeded (admin, vendor, client)
- [ ] Node modules installed
- [ ] `npm run dev` is running (in one terminal)
- [ ] `php artisan serve` is running (in another terminal)
- [ ] Application opens at http://localhost:8000
- [ ] Can register/login successfully
- [ ] Admin user created and can login

---

## 📂 Project Structure

```
c:\xampp\htdocs\kabz\
├── app/
│   ├── Http/Controllers/          # Your controllers will go here
│   ├── Models/
│   │   └── User.php               # ✓ Updated with HasRoles trait
│   └── ...
├── database/
│   ├── migrations/                # ✓ All migrations ready
│   └── seeders/
│       ├── RoleSeeder.php         # ✓ Your custom seeder
│       └── DatabaseSeeder.php     # ✓ Main seeder
├── resources/
│   └── views/                     # ✓ Breeze views installed
├── routes/
│   ├── web.php                    # ✓ Auth routes configured
│   └── auth.php                   # ✓ Breeze auth routes
├── .env                           # ✓ Configured
├── composer.phar                  # ✓ Composer installed
├── create-database.sql            # Helper SQL script
├── run-migrations.bat             # Quick migration script
└── Docs/                          # All documentation
```

---

## 🛠️ Daily Development Workflow

### Start Your Day:

```bash
# Terminal 1: Start frontend dev server
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

### Make Changes:
- Edit files in `app/`, `resources/views/`, etc.
- Frontend changes auto-reload
- Backend changes reload on refresh

### End Your Day:
- Press `Ctrl+C` in both terminals
- Commit your changes to Git

---

## 🐛 Troubleshooting

### Issue: "Access denied for user 'root'@'localhost'"

**Solution:**
1. Check if MySQL is running in XAMPP
2. Update `.env` with correct password:
   ```env
   DB_PASSWORD=your_password
   ```
3. Or reset MySQL password in XAMPP

### Issue: "Base table or view not found"

**Solution:**
```bash
php artisan migrate:fresh
php artisan db:seed --class=RoleSeeder
```

### Issue: "npm command not found"

**Solution:**
- Install Node.js from https://nodejs.org/
- Restart terminal after installation

### Issue: "Port 8000 already in use"

**Solution:**
```bash
# Use different port
php artisan serve --port=8001
```

### Issue: "Mix manifest not found"

**Solution:**
```bash
npm install
npm run dev
```

---

## 📚 What You Can Do Now

### 1. Test Authentication
- Register a new user
- Login/logout
- Reset password flow

### 2. Verify Roles
```bash
php artisan tinker
User::first()->getRoleNames();
```

### 3. Start Building Features

**Next Development Steps:**
1. Create Vendor model and migration
2. Create Category model and migration  
3. Create Service model and migration
4. Build vendor registration flow
5. Design vendor profile pages

---

## 🎯 Core Features to Build (Phase 2)

### Vendor Management
- Vendor registration form
- Profile management
- Service listing CRUD
- Media gallery upload

### Models to Create
```bash
php artisan make:model Vendor -mcr
php artisan make:model Category -mcr
php artisan make:model Service -mcr
php artisan make:model Review -mcr
```

This generates:
- Model file
- Migration file
- Controller file
- Resource routes

---

## 📖 Important Commands Reference

```bash
# Database
php artisan migrate                      # Run migrations
php artisan migrate:fresh               # Fresh migration
php artisan migrate:rollback            # Rollback
php artisan db:seed                     # Run seeders

# Make Files
php artisan make:model ModelName -mcr   # Model + Migration + Controller
php artisan make:controller Name        # Controller
php artisan make:migration create_x     # Migration

# Cache
php artisan optimize:clear              # Clear all cache
php artisan config:cache                # Cache config
php artisan route:cache                 # Cache routes

# Testing
php artisan tinker                      # Laravel REPL
php artisan route:list                  # List all routes

# Server
php artisan serve                       # Start server
php artisan serve --port=8001          # Different port
```

---

## 🎊 Success!

Your KABZS EVENT project is now ready for development!

**What's Working:**
✅ Laravel 10 installed  
✅ Authentication (Breeze) configured  
✅ Role-based access control (Spatie Permission) ready  
✅ Database structure planned  
✅ Development environment set up  

**Database:** `event_management_db`  
**Roles:** admin, vendor, client  
**Tech Stack:** Laravel 10 + Blade + Tailwind CSS + MySQL  

---

## 🚀 Next Actions

1. **Complete Steps 1-9 above** to get the application running
2. **Test authentication** - register and login
3. **Create admin user** - via Tinker
4. **Start building** - Create vendor features (Phase 2)

---

## 📞 Need Help?

- **Documentation:** Check `/Docs/` folder
- **Laravel Docs:** https://laravel.com/docs/10.x
- **Spatie Permission:** https://spatie.be/docs/laravel-permission
- **Tailwind CSS:** https://tailwindcss.com/docs

---

**🎉 Happy Coding! Welcome to KABZS EVENT Development!**

---

**Quick Reference:**
- **Database:** event_management_db
- **App URL:** http://localhost:8000
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Project Path:** C:\xampp\htdocs\kabz\

