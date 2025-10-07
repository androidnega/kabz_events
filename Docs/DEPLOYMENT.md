# 🚀 KABZS EVENT - Deployment Guide

## Production Deployment Checklist

---

## 🎯 Pre-Deployment Checklist

### Environment Configuration
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_KEY`
- [ ] Configure production database credentials
- [ ] Set up Redis for cache and queues
- [ ] Configure production mail service
- [ ] Set up file storage (S3/Spaces)
- [ ] Configure backup strategy

### Security
- [ ] Enable HTTPS/SSL
- [ ] Set secure session settings
- [ ] Configure CORS if needed
- [ ] Set up firewall rules
- [ ] Enable rate limiting
- [ ] Configure security headers
- [ ] Review and update `.env` secrets

### Performance
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Enable OPcache
- [ ] Configure CDN for assets

---

## 🛠️ Deployment Options

### Option 1: Laravel Forge (Recommended)

**Why Forge?**
- One-click Laravel deployment
- Automatic SSL via Let's Encrypt
- Built-in deployment script
- Server monitoring
- Queue management
- Scheduled jobs

**Steps:**

1. **Create Server on Forge**
   - Choose provider (DigitalOcean, AWS, etc.)
   - Select PHP 8.2+
   - Enable MySQL, Redis, Nginx

2. **Connect Git Repository**
   ```bash
   # Push your code to GitHub/GitLab/Bitbucket
   git remote add origin <your-repo-url>
   git push -u origin main
   ```

3. **Create Site on Forge**
   - Domain: `kabzsevent.com`
   - Project type: Laravel
   - Web directory: `/public`

4. **Configure Environment**
   - Update `.env` via Forge dashboard
   - Set database credentials
   - Configure Redis

5. **Deploy Script** (Forge auto-generates)
   ```bash
   cd /home/forge/kabzsevent.com
   git pull origin main
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan queue:restart
   npm install
   npm run build
   ```

6. **Enable Queue Worker**
   - Queue: `redis`
   - Connection: `redis`
   - Processes: `1` (scale as needed)

7. **Setup Scheduled Jobs**
   - Forge handles Laravel scheduler automatically

---

### Option 2: DigitalOcean App Platform

**Steps:**

1. **Push to GitHub**
   ```bash
   git remote add origin <repo-url>
   git push -u origin main
   ```

2. **Create App on DigitalOcean**
   - Connect GitHub repository
   - Choose branch: `main`

3. **Configure Build Settings**
   ```yaml
   # Build Command
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm install && npm run build
   
   # Run Command
   heroku-php-nginx -C nginx.conf public/
   ```

4. **Add Database**
   - Add MySQL managed database
   - Update environment variables

5. **Add Redis**
   - Add managed Redis cluster

6. **Configure Environment Variables**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<generated-key>
   DB_CONNECTION=mysql
   DB_HOST=<db-host>
   DB_DATABASE=event_management_db
   REDIS_HOST=<redis-host>
   ```

7. **Setup Worker for Queues**
   - Add worker component
   - Run: `php artisan queue:work redis --sleep=3 --tries=3`

---

### Option 3: AWS (EC2 + RDS)

**Architecture:**
```
CloudFront (CDN)
    │
Elastic Load Balancer
    │
EC2 Instances (Auto Scaling)
    │
RDS (MySQL)
ElastiCache (Redis)
S3 (Media Storage)
```

**Steps:**

1. **Launch EC2 Instance**
   - Ubuntu 22.04 LTS
   - t3.medium or larger
   - Install LEMP stack

2. **Setup RDS MySQL**
   ```bash
   # Create database
   CREATE DATABASE event_management_db;
   ```

3. **Setup ElastiCache Redis**
   - Choose Redis cluster
   - Configure security groups

4. **Deploy Application**
   ```bash
   # SSH into EC2
   ssh ubuntu@<ec2-ip>
   
   # Install dependencies
   sudo apt update
   sudo apt install nginx php8.2-fpm php8.2-mysql php8.2-redis composer
   
   # Clone repository
   git clone <repo-url> /var/www/kabzs-event
   cd /var/www/kabzs-event
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   
   # Set permissions
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   
   # Configure environment
   cp .env.example .env
   php artisan key:generate
   
   # Run migrations
   php artisan migrate --force
   php artisan db:seed --class=RoleSeeder --force
   
   # Optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Configure Nginx**
   ```nginx
   # /etc/nginx/sites-available/kabzs-event
   server {
       listen 80;
       server_name kabzsevent.com www.kabzsevent.com;
       root /var/www/kabzs-event/public;
   
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
   
       index index.php;
   
       charset utf-8;
   
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
   
       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }
   
       error_page 404 /index.php;
   
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

6. **Setup SSL with Let's Encrypt**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d kabzsevent.com -d www.kabzsevent.com
   ```

7. **Setup Supervisor for Queues**
   ```ini
   # /etc/supervisor/conf.d/kabzs-event-worker.conf
   [program:kabzs-event-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/kabzs-event/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/kabzs-event/storage/logs/worker.log
   stopwaitsecs=3600
   ```

   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start "kabzs-event-worker:*"
   ```

8. **Setup Cron for Laravel Scheduler**
   ```bash
   crontab -e
   
   # Add:
   * * * * * cd /var/www/kabzs-event && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## 🔐 Production Environment Variables

```env
# Application
APP_NAME="KABZS EVENT"
APP_ENV=production
APP_KEY=base64:GENERATE_SECURE_KEY_HERE
APP_DEBUG=false
APP_URL=https://kabzsevent.com

# Database (Use managed service)
DB_CONNECTION=mysql
DB_HOST=production-db-host.amazonaws.com
DB_PORT=3306
DB_DATABASE=event_management_db
DB_USERNAME=kabzs_user
DB_PASSWORD=SECURE_PASSWORD_HERE

# Redis (Use managed service)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=production-redis-host.amazonaws.com
REDIS_PASSWORD=SECURE_PASSWORD_HERE
REDIS_PORT=6379

# Mail (Use service like SES, Mailgun)
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@kabzsevent.com
MAIL_FROM_NAME="KABZS EVENT"
AWS_ACCESS_KEY_ID=your-ses-key
AWS_SECRET_ACCESS_KEY=your-ses-secret
AWS_DEFAULT_REGION=us-east-1

# File Storage (S3 or DigitalOcean Spaces)
FILESYSTEM_DISK=s3
AWS_BUCKET=kabzs-event-production
AWS_URL=https://kabzs-event-production.s3.amazonaws.com

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Security
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
```

---

## 🔄 Continuous Deployment

### GitHub Actions Workflow

`.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
    
    - name: Install Dependencies
      run: composer install --optimize-autoloader --no-dev
    
    - name: Run Tests
      run: php artisan test
    
    - name: Deploy to Server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.SERVER_HOST }}
        username: ${{ secrets.SERVER_USER }}
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          cd /var/www/kabzs-event
          git pull origin main
          composer install --optimize-autoloader --no-dev
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan queue:restart
          npm install && npm run build
```

---

## 📊 Monitoring & Logging

### Services to Setup

1. **Error Tracking: Sentry**
   ```bash
   composer require sentry/sentry-laravel
   php artisan sentry:publish --dsn=https://your-dsn@sentry.io/project-id
   ```

2. **Application Monitoring: New Relic**
   ```bash
   # Install New Relic PHP agent
   ```

3. **Uptime Monitoring: UptimeRobot**
   - Monitor: https://kabzsevent.com
   - Check interval: 5 minutes

4. **Log Management: Papertrail or Loggly**

---

## 💾 Backup Strategy

### Database Backup

```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS event_management_db > backup_$DATE.sql
aws s3 cp backup_$DATE.sql s3://kabzs-backups/database/
```

### Storage Backup

- Use S3 versioning
- Enable cross-region replication

### Schedule Backup

```bash
# Crontab
0 2 * * * /path/to/backup-script.sh
```

---

## 🔍 Performance Optimization

### Enable OPcache

`/etc/php/8.2/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
```

### Database Indexing

```sql
-- Ensure all foreign keys are indexed
-- Add composite indexes for frequent queries
CREATE INDEX idx_vendors_city_verified ON vendors(city, is_verified);
CREATE INDEX idx_services_vendor_category ON services(vendor_id, category_id);
```

### CDN Setup

- Use CloudFlare or AWS CloudFront
- Cache static assets (CSS, JS, images)
- Configure cache headers

---

## 📱 Post-Deployment Tasks

- [ ] Test authentication flow
- [ ] Test vendor registration
- [ ] Test search functionality
- [ ] Test file uploads
- [ ] Verify email sending
- [ ] Test payment processing (if applicable)
- [ ] Check queue processing
- [ ] Verify scheduled tasks
- [ ] Test on mobile devices
- [ ] Run security scan
- [ ] Check page load times
- [ ] Verify SSL certificate
- [ ] Test backup restore

---

## 🆘 Rollback Plan

```bash
# Quick rollback
cd /var/www/kabzs-event
git reset --hard HEAD~1
composer install --optimize-autoloader --no-dev
php artisan migrate:rollback --step=1
php artisan config:cache
php artisan queue:restart
sudo systemctl restart php8.2-fpm
```

---

## 📞 Support & Maintenance

- Monitor error logs daily
- Review server metrics weekly
- Update dependencies monthly
- Security patches: immediately
- Laravel upgrades: quarterly

---

**🚀 Deployment complete! KABZS EVENT is live.**

