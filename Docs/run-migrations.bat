@echo off
echo ========================================
echo    KABZS EVENT - Run Migrations
echo ========================================
echo.

echo [1/3] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Migrations failed!
    echo.
    echo Make sure:
    echo 1. MySQL is running in XAMPP
    echo 2. Database 'event_management_db' exists
    echo 3. .env file has correct database credentials
    echo.
    echo To create database, run this in MySQL:
    echo CREATE DATABASE event_management_db;
    echo.
    pause
    exit /b 1
)
echo √ Migrations completed
echo.

echo [2/3] Seeding roles and permissions...
php artisan db:seed --class=RoleSeeder
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Seeding failed!
    pause
    exit /b 1
)
echo √ Roles seeded (admin, vendor, client)
echo.

echo [3/3] Installing Node dependencies...
if exist node_modules (
    echo √ Node modules already installed
) else (
    call npm install
    echo √ Node modules installed
)
echo.

echo ========================================
echo    Setup Complete!
echo ========================================
echo.
echo Database: event_management_db
echo Roles created: admin, vendor, client
echo.
echo Next steps:
echo 1. Run: npm run dev (in another terminal)
echo 2. Run: php artisan serve
echo 3. Open: http://localhost:8000
echo.
echo To create an admin user, run: php artisan tinker
echo Then in tinker:
echo   $user = User::create([
echo       'name' =^> 'Admin',
echo       'email' =^> 'admin@kabzsevent.com',
echo       'password' =^> bcrypt('password123'),
echo       'email_verified_at' =^> now(),
echo   ]);
echo   $user-^>assignRole('admin');
echo.
pause

