# KABZS EVENT - Automated Setup Script for Windows
# This script will install Composer and set up the Laravel project

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   KABZS EVENT - Automated Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if PHP is installed
Write-Host "[1/10] Checking PHP installation..." -ForegroundColor Yellow
$phpVersion = php --version 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ PHP is installed: $($phpVersion.Split("`n")[0])" -ForegroundColor Green
} else {
    Write-Host "✗ PHP is not installed. Please install XAMPP or PHP first." -ForegroundColor Red
    exit 1
}

# Download and install Composer
Write-Host "`n[2/10] Installing Composer..." -ForegroundColor Yellow
if (Test-Path "composer.phar") {
    Write-Host "✓ Composer already exists" -ForegroundColor Green
} else {
    Write-Host "Downloading Composer installer..." -ForegroundColor Yellow
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    
    Write-Host "Installing Composer..." -ForegroundColor Yellow
    php composer-setup.php --quiet
    
    Write-Host "Cleaning up..." -ForegroundColor Yellow
    Remove-Item composer-setup.php
    
    if (Test-Path "composer.phar") {
        Write-Host "✓ Composer installed successfully" -ForegroundColor Green
    } else {
        Write-Host "✗ Failed to install Composer" -ForegroundColor Red
        exit 1
    }
}

# Create Laravel project
Write-Host "`n[3/10] Creating Laravel 10 project..." -ForegroundColor Yellow
if (Test-Path "vendor") {
    Write-Host "✓ Laravel project already exists" -ForegroundColor Green
} else {
    php composer.phar create-project laravel/laravel:^10.0 temp-laravel --prefer-dist
    
    # Move Laravel files to current directory
    Write-Host "Moving Laravel files..." -ForegroundColor Yellow
    Get-ChildItem -Path "temp-laravel" -Force | Move-Item -Destination . -Force
    Remove-Item "temp-laravel" -Force -Recurse
    
    Write-Host "✓ Laravel 10 project created" -ForegroundColor Green
}

# Install Laravel Breeze
Write-Host "`n[4/10] Installing Laravel Breeze..." -ForegroundColor Yellow
php composer.phar require laravel/breeze --dev
Write-Host "✓ Laravel Breeze installed" -ForegroundColor Green

# Install Breeze scaffolding
Write-Host "`n[5/10] Installing Breeze scaffolding (Blade + Tailwind)..." -ForegroundColor Yellow
php artisan breeze:install blade --quiet
Write-Host "✓ Breeze scaffolding installed" -ForegroundColor Green

# Install Spatie Permission
Write-Host "`n[6/10] Installing Spatie Laravel Permission..." -ForegroundColor Yellow
php composer.phar require spatie/laravel-permission
Write-Host "✓ Spatie Permission installed" -ForegroundColor Green

# Install Spatie Media Library
Write-Host "`n[7/10] Installing Spatie Laravel Media Library..." -ForegroundColor Yellow
php composer.phar require spatie/laravel-medialibrary
Write-Host "✓ Spatie Media Library installed" -ForegroundColor Green

# Configure .env file
Write-Host "`n[8/10] Configuring environment..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "Updating .env file..." -ForegroundColor Yellow
    $envContent = Get-Content ".env" -Raw
    
    # Update app name
    $envContent = $envContent -replace 'APP_NAME=.*', 'APP_NAME="KABZS EVENT"'
    
    # Update database configuration
    $envContent = $envContent -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=mysql'
    $envContent = $envContent -replace 'DB_HOST=.*', 'DB_HOST=127.0.0.1'
    $envContent = $envContent -replace 'DB_PORT=.*', 'DB_PORT=3306'
    $envContent = $envContent -replace 'DB_DATABASE=.*', 'DB_DATABASE=event_management_db'
    $envContent = $envContent -replace 'DB_USERNAME=.*', 'DB_USERNAME=root'
    $envContent = $envContent -replace 'DB_PASSWORD=.*', 'DB_PASSWORD='
    
    # Update session driver
    $envContent = $envContent -replace 'SESSION_DRIVER=.*', 'SESSION_DRIVER=file'
    
    Set-Content ".env" $envContent
    Write-Host "✓ Environment configured" -ForegroundColor Green
} else {
    Copy-Item ".env.example" ".env"
    Write-Host "✓ .env file created" -ForegroundColor Green
}

# Generate application key
Write-Host "`n[9/10] Generating application key..." -ForegroundColor Yellow
php artisan key:generate
Write-Host "✓ Application key generated" -ForegroundColor Green

# Publish vendor configurations
Write-Host "`n[10/10] Publishing vendor configurations..." -ForegroundColor Yellow
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --quiet
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations" --quiet
Write-Host "✓ Vendor configurations published" -ForegroundColor Green

# Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Create database 'event_management_db' in MySQL" -ForegroundColor White
Write-Host "2. Run: php artisan migrate" -ForegroundColor White
Write-Host "3. Run: php artisan db:seed --class=RoleSeeder" -ForegroundColor White
Write-Host "4. Run: npm install && npm run dev" -ForegroundColor White
Write-Host "5. Run: php artisan serve" -ForegroundColor White
Write-Host "6. Open: http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "Database: event_management_db" -ForegroundColor Cyan
Write-Host "Username: root" -ForegroundColor Cyan
Write-Host "Password: (empty)" -ForegroundColor Cyan
Write-Host ""


