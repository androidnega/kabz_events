# ⚡ KABZS EVENT - Quick Start Guide

## 🎯 Get Up and Running in 10 Minutes

This is the fastest way to get KABZS EVENT running on your machine.

---

## ✅ Prerequisites

Make sure you have:
- ✅ **Docker Desktop** installed and **running**
- ✅ **Git** installed
- ✅ **Node.js** (v18+) and **NPM** installed

---

## 🚀 Installation (5 Commands)

### 1. Create the Project
```bash
curl -s "https://laravel.build/kabzs-event?with=mysql,redis,mailpit" | bash
```
⏱️ This takes 2-3 minutes

### 2. Navigate to Project
```bash
cd kabzs-event
```

### 3. Start Docker Containers
```bash
./vendor/bin/sail up -d
```
⏱️ First start takes 1-2 minutes

### 4. Install Packages
```bash
# Install Breeze
./vendor/bin/sail composer require laravel/breeze --dev
./vendor/bin/sail artisan breeze:install blade

# Install Spatie packages
./vendor/bin/sail composer require spatie/laravel-permission spatie/laravel-medialibrary

# Publish configurations
./vendor/bin/sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
./vendor/bin/sail artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

### 5. Setup Database
```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Seed roles (copy the RoleSeeder from the project files first)
./vendor/bin/sail artisan db:seed --class=RoleSeeder
```

### 6. Build Frontend
```bash
npm install && npm run dev
```

---

## 🎉 Access Your Application

Open your browser:
- **Main App**: http://localhost
- **Email Testing**: http://localhost:8025

---

## 📝 Configure Environment

Edit `.env` file and ensure these values:
```env
APP_NAME="KABZS EVENT"
DB_DATABASE=event_management_db
```

Then refresh config:
```bash
./vendor/bin/sail artisan config:clear
```

---

## 👤 Create Test Admin User

```bash
./vendor/bin/sail artisan tinker
```

In Tinker console:
```php
$user = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@kabzsevent.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);

$user->assignRole('admin');

echo "Admin created! Login with: admin@kabzsevent.com / password123";
exit
```

---

## 🛑 Stop the Application

```bash
./vendor/bin/sail down
```

---

## 🔄 Daily Workflow

**Start working:**
```bash
./vendor/bin/sail up -d
npm run dev
```

**Stop working:**
```bash
./vendor/bin/sail down
```

---

## 🐛 Troubleshooting

### Port 80 is busy
```bash
# Stop Apache/XAMPP first
# Or edit docker-compose.yml to use port 8080
```

### Can't connect to database
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

### Permission errors
```bash
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📚 Next Steps

1. ✅ **Read SETUP.md** for detailed information
2. ✅ **Read ARCHITECTURE.md** for system design
3. ✅ **Read INSTALLATION_COMMANDS.md** for all commands
4. ✅ **Start building features** (vendors, categories, etc.)

---

## 💡 Pro Tips

### Create Sail Alias
```bash
# Add to your shell profile (.bashrc, .zshrc)
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# Now use:
sail up -d
sail artisan migrate
```

### Useful Commands
```bash
# Clear all cache
sail artisan optimize:clear

# View routes
sail artisan route:list

# Access database
sail mysql

# View logs
sail logs -f

# Run tests
sail artisan test
```

---

**🎊 You're all set! Welcome to KABZS EVENT development.**

