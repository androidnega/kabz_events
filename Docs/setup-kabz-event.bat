@echo off
REM KABZS EVENT - Automated Setup Script for Windows (Batch)
echo ========================================
echo    KABZS EVENT - Automated Setup
echo ========================================
echo.

REM Check PHP
echo [1/10] Checking PHP installation...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo X PHP is not installed. Please install XAMPP or PHP first.
    pause
    exit /b 1
)
echo √ PHP is installed
echo.

REM Install Composer
echo [2/10] Installing Composer...
if exist composer.phar (
    echo √ Composer already exists
) else (
    echo Downloading Composer...
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    echo Installing Composer...
    php composer-setup.php --quiet
    del composer-setup.php
    echo √ Composer installed
)
echo.

REM Create Laravel project
echo [3/10] Creating Laravel 10 project...
if exist vendor (
    echo √ Laravel project already exists
) else (
    echo This will take a few minutes...
    php composer.phar create-project laravel/laravel:^10.0 temp-laravel --prefer-dist --no-interaction
    xcopy temp-laravel\* . /E /H /Y /Q
    rmdir /s /q temp-laravel
    echo √ Laravel 10 created
)
echo.

REM Install Breeze
echo [4/10] Installing Laravel Breeze...
php composer.phar require laravel/breeze --dev --no-interaction
echo √ Breeze installed
echo.

REM Install Breeze scaffolding
echo [5/10] Installing Breeze scaffolding...
php artisan breeze:install blade --no-interaction
echo √ Breeze scaffolding installed
echo.

REM Install Spatie Permission
echo [6/10] Installing Spatie Permission...
php composer.phar require spatie/laravel-permission --no-interaction
echo √ Spatie Permission installed
echo.

REM Install Spatie Media Library
echo [7/10] Installing Spatie Media Library...
php composer.phar require spatie/laravel-medialibrary --no-interaction
echo √ Spatie Media Library installed
echo.

REM Configure environment
echo [8/10] Configuring environment...
if exist .env (
    powershell -Command "(gc .env) -replace 'APP_NAME=.*', 'APP_NAME=\"KABZS EVENT\"' | Out-File -encoding ASCII .env"
    powershell -Command "(gc .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=event_management_db' | Out-File -encoding ASCII .env"
    powershell -Command "(gc .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=root' | Out-File -encoding ASCII .env"
    powershell -Command "(gc .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=' | Out-File -encoding ASCII .env"
    echo √ Environment configured
) else (
    copy .env.example .env
    echo √ .env created
)
echo.

REM Generate key
echo [9/10] Generating application key...
php artisan key:generate --no-interaction
echo √ Key generated
echo.

REM Publish configurations
echo [10/10] Publishing configurations...
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations" --no-interaction
echo √ Configurations published
echo.

echo ========================================
echo    Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Create database 'event_management_db' in MySQL
echo 2. Run: php artisan migrate
echo 3. Run: php artisan db:seed --class=RoleSeeder
echo 4. Run: npm install ^&^& npm run dev
echo 5. Run: php artisan serve
echo 6. Open: http://localhost:8000
echo.
echo Database: event_management_db
echo Username: root
echo Password: (empty)
echo.
pause


