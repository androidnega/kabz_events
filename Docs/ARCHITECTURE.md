# 🏗️ KABZS EVENT - System Architecture

## Overview

This document outlines the architecture, design patterns, and technical decisions for the KABZS EVENT platform.

---

## 🎯 System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Client Browser                        │
│              (Blade Views + Tailwind CSS)               │
└─────────────────┬───────────────────────────────────────┘
                  │ HTTP/HTTPS
                  ▼
┌─────────────────────────────────────────────────────────┐
│                   Nginx Web Server                       │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│            Laravel 10 Application Layer                  │
│  ┌───────────────────────────────────────────────────┐ │
│  │              Routes (web.php)                      │ │
│  └────────────────┬──────────────────────────────────┘ │
│                   │                                      │
│  ┌────────────────▼──────────────────────────────────┐ │
│  │           Middleware Layer                         │ │
│  │  • Authentication (Breeze)                        │ │
│  │  • Authorization (Spatie Permission)              │ │
│  │  • CSRF Protection                                │ │
│  │  • Rate Limiting                                  │ │
│  └────────────────┬──────────────────────────────────┘ │
│                   │                                      │
│  ┌────────────────▼──────────────────────────────────┐ │
│  │              Controllers                           │ │
│  │  • AdminController                                │ │
│  │  • VendorController                               │ │
│  │  • ClientController                               │ │
│  └────────────────┬──────────────────────────────────┘ │
│                   │                                      │
│  ┌────────────────▼──────────────────────────────────┐ │
│  │            Business Logic                          │ │
│  │  • Models (Eloquent)                              │ │
│  │  • Services                                       │ │
│  │  • Policies                                       │ │
│  └────────────────┬──────────────────────────────────┘ │
└───────────────────┼──────────────────────────────────────┘
                    │
        ┌───────────┼───────────┐
        │           │           │
        ▼           ▼           ▼
┌──────────┐  ┌─────────┐  ┌────────┐
│  MySQL   │  │  Redis  │  │ Files  │
│   (DB)   │  │ (Cache) │  │ (S3)   │
└──────────┘  └─────────┘  └────────┘
```

---

## 📂 Directory Structure

### MVC Pattern Organization

```
app/
├── Console/
│   └── Commands/              # Custom Artisan commands
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Breeze auth controllers
│   │   ├── Admin/             # Admin panel controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── VendorVerificationController.php
│   │   │   ├── UserManagementController.php
│   │   │   └── FeaturedAdController.php
│   │   ├── Vendor/            # Vendor dashboard controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ServiceController.php
│   │   │   └── MediaController.php
│   │   └── Client/            # Public-facing controllers
│   │       ├── VendorController.php
│   │       ├── ReviewController.php
│   │       ├── BookmarkController.php
│   │       └── SearchController.php
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php
│   │   ├── EnsureUserIsVendor.php
│   │   └── CheckVendorVerification.php
│   ├── Requests/              # Form request validation
│   │   ├── VendorRequest.php
│   │   ├── ServiceRequest.php
│   │   └── ReviewRequest.php
│   └── Resources/             # API resources (future)
├── Models/
│   ├── User.php
│   ├── Vendor.php
│   ├── Category.php
│   ├── Service.php
│   ├── Review.php
│   ├── Bookmark.php
│   ├── VendorSubscription.php
│   ├── FeaturedAd.php
│   └── VerificationRequest.php
├── Policies/                  # Authorization policies
│   ├── VendorPolicy.php
│   ├── ServicePolicy.php
│   └── ReviewPolicy.php
├── Providers/
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php
│   └── EventServiceProvider.php
└── Services/                  # Business logic services
    ├── VendorService.php
    ├── VerificationService.php
    └── MediaService.php
```

---

## 🗄️ Database Schema

### Core Tables

#### users
```sql
id                  bigint (PK)
name                varchar(255)
email               varchar(255) UNIQUE
email_verified_at   timestamp NULL
password            varchar(255)
remember_token      varchar(100) NULL
created_at          timestamp
updated_at          timestamp
```

#### vendors
```sql
id                  bigint (PK)
user_id             bigint (FK -> users.id) UNIQUE
business_name       varchar(255)
slug                varchar(255) UNIQUE
description         text
phone               varchar(20)
address             text
city                varchar(100)
state               varchar(100)
postal_code         varchar(20)
country             varchar(100) DEFAULT 'Philippines'
is_verified         boolean DEFAULT false
verification_status enum('pending','approved','rejected') DEFAULT 'pending'
verified_at         timestamp NULL
featured_until      timestamp NULL
profile_views       integer DEFAULT 0
created_at          timestamp
updated_at          timestamp

INDEX(user_id)
INDEX(is_verified)
INDEX(slug)
INDEX(city)
```

#### categories
```sql
id                  bigint (PK)
name                varchar(255)
slug                varchar(255) UNIQUE
description         text NULL
icon                varchar(100) NULL
is_active           boolean DEFAULT true
display_order       integer DEFAULT 0
created_at          timestamp
updated_at          timestamp

INDEX(slug)
INDEX(is_active)
```

#### services
```sql
id                  bigint (PK)
vendor_id           bigint (FK -> vendors.id)
category_id         bigint (FK -> categories.id)
name                varchar(255)
slug                varchar(255)
description         text
price_min           decimal(10,2) NULL
price_max           decimal(10,2) NULL
price_type          enum('fixed','hourly','package','quote')
is_available        boolean DEFAULT true
created_at          timestamp
updated_at          timestamp

INDEX(vendor_id)
INDEX(category_id)
INDEX(slug)
INDEX(is_available)
```

#### reviews
```sql
id                  bigint (PK)
vendor_id           bigint (FK -> vendors.id)
user_id             bigint (FK -> users.id)
rating              tinyint (1-5)
title               varchar(255) NULL
comment             text NULL
is_verified         boolean DEFAULT false
is_published        boolean DEFAULT true
helpful_count       integer DEFAULT 0
created_at          timestamp
updated_at          timestamp

INDEX(vendor_id)
INDEX(user_id)
INDEX(rating)
UNIQUE(vendor_id, user_id) # One review per user per vendor
```

#### bookmarks
```sql
id                  bigint (PK)
user_id             bigint (FK -> users.id)
vendor_id           bigint (FK -> vendors.id)
created_at          timestamp
updated_at          timestamp

UNIQUE(user_id, vendor_id)
INDEX(user_id)
INDEX(vendor_id)
```

#### vendor_subscriptions
```sql
id                  bigint (PK)
vendor_id           bigint (FK -> vendors.id)
plan_name           enum('free','basic','premium','enterprise')
price               decimal(10,2)
billing_cycle       enum('monthly','yearly')
starts_at           timestamp
ends_at             timestamp
is_active           boolean DEFAULT true
auto_renew          boolean DEFAULT true
created_at          timestamp
updated_at          timestamp

INDEX(vendor_id)
INDEX(is_active)
INDEX(ends_at)
```

#### featured_ads
```sql
id                  bigint (PK)
vendor_id           bigint (FK -> vendors.id)
placement           enum('homepage','category','search')
starts_at           timestamp
ends_at             timestamp
price_paid          decimal(10,2)
impressions         integer DEFAULT 0
clicks              integer DEFAULT 0
is_active           boolean DEFAULT true
created_at          timestamp
updated_at          timestamp

INDEX(vendor_id)
INDEX(placement)
INDEX(is_active)
INDEX(starts_at, ends_at)
```

#### verification_requests
```sql
id                  bigint (PK)
vendor_id           bigint (FK -> vendors.id)
status              enum('pending','approved','rejected') DEFAULT 'pending'
business_permit     varchar(255) NULL
valid_id            varchar(255) NULL
proof_of_work       varchar(255) NULL
notes               text NULL
reviewed_by         bigint (FK -> users.id) NULL
reviewed_at         timestamp NULL
created_at          timestamp
updated_at          timestamp

INDEX(vendor_id)
INDEX(status)
```

---

## 🔐 Authentication & Authorization

### Authentication (Laravel Breeze)
- Email/Password login
- Registration
- Email verification
- Password reset
- Remember me functionality

### Authorization (Spatie Permission)

**Role Hierarchy:**
```
Admin (SuperUser)
├── Full system access
├── Vendor verification
├── User management
└── Featured ads management

Vendor (Business User)
├── Profile management
├── Service management
├── Media uploads
└── View inquiries

Client (End User)
├── Browse vendors
├── Leave reviews
├── Bookmark vendors
└── Contact vendors
```

**Permission Examples:**
- `access admin panel`
- `verify vendors`
- `manage featured ads`
- `create services`
- `contact vendors`
- `leave review`

---

## 🎨 Frontend Architecture

### Blade Component Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Main layout
│   ├── admin.blade.php        # Admin panel layout
│   ├── vendor.blade.php       # Vendor dashboard layout
│   └── guest.blade.php        # Guest/public layout
├── components/
│   ├── nav/
│   │   ├── main-nav.blade.php
│   │   ├── admin-sidebar.blade.php
│   │   └── vendor-sidebar.blade.php
│   ├── vendor/
│   │   ├── card.blade.php
│   │   ├── profile-header.blade.php
│   │   └── service-item.blade.php
│   └── ui/
│       ├── button.blade.php
│       ├── input.blade.php
│       ├── modal.blade.php
│       └── alert.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── vendors/
│   │   ├── index.blade.php
│   │   ├── verify.blade.php
│   │   └── show.blade.php
│   └── users/
├── vendor/
│   ├── dashboard.blade.php
│   ├── profile/
│   ├── services/
│   └── media/
└── client/
    ├── home.blade.php
    ├── vendors/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   └── search.blade.php
    └── bookmarks/
```

### Tailwind CSS Organization

```css
/* resources/css/app.css */

@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom Components */
@layer components {
  .btn-primary { /* ... */ }
  .card { /* ... */ }
  .vendor-card { /* ... */ }
}
```

---

## ⚡ Performance Optimization

### Caching Strategy (Redis)

**Cache Layers:**
1. **Query Cache** - Frequently accessed data
2. **Session Cache** - User sessions
3. **Route Cache** - Compiled routes
4. **View Cache** - Compiled Blade templates
5. **Config Cache** - Application configuration

**Implementation:**
```php
// Cache vendor list for 1 hour
$vendors = Cache::remember('vendors.featured', 3600, function () {
    return Vendor::where('is_verified', true)
        ->where('featured_until', '>', now())
        ->get();
});
```

### Queue System (Redis)

**Background Jobs:**
- Email notifications
- Image processing
- Report generation
- Analytics calculation
- Vendor verification notifications

---

## 🔍 Search Architecture (Future)

### Meilisearch Integration

**Searchable Entities:**
- Vendors (name, business_name, description, city)
- Services (name, description)
- Categories (name, description)

**Search Features:**
- Typo tolerance
- Synonyms
- Filters (category, location, rating)
- Sorting (relevance, rating, price)
- Faceted search

---

## 📊 Analytics & Reporting

### Metrics to Track

**Vendor Metrics:**
- Profile views
- Service views
- Contact inquiries
- Review count & average rating

**Platform Metrics:**
- Total vendors (verified/unverified)
- Total clients
- Search queries
- Popular categories

---

## 🚀 Deployment Architecture

### Production Environment

```
┌─────────────────┐
│   CloudFlare    │  CDN & DDoS Protection
└────────┬────────┘
         │
┌────────▼────────┐
│  Load Balancer  │  Nginx/HAProxy
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼──┐  ┌───▼──┐
│ App  │  │ App  │  Laravel (PHP-FPM)
│ Srv1 │  │ Srv2 │
└───┬──┘  └───┬──┘
    │         │
    └────┬────┘
         │
┌────────▼────────┐
│   MySQL (RDS)   │  Database Cluster
└─────────────────┘
         │
┌────────▼────────┐
│ Redis (Cluster) │  Cache & Queues
└─────────────────┘
         │
┌────────▼────────┐
│   S3 / Spaces   │  Media Storage
└─────────────────┘
```

---

## 🔒 Security Measures

1. **Input Validation** - Form requests
2. **SQL Injection Protection** - Eloquent ORM
3. **XSS Protection** - Blade escaping
4. **CSRF Protection** - Token validation
5. **Rate Limiting** - Throttle middleware
6. **Authentication** - Bcrypt password hashing
7. **Authorization** - Policy-based access control
8. **HTTPS Enforcement** - SSL/TLS
9. **Security Headers** - CSP, X-Frame-Options

---

## 📦 Third-Party Integrations (Planned)

- **Payment:** Stripe, PayPal
- **Email:** SendGrid, AWS SES
- **Storage:** AWS S3, DigitalOcean Spaces
- **Maps:** Google Maps API
- **Analytics:** Google Analytics, Mixpanel
- **Monitoring:** Sentry, New Relic

---

## 🧪 Testing Strategy

### Test Pyramid

```
         /\
        /  \     E2E Tests (Browser)
       /────\    
      /      \   Integration Tests
     /────────\  
    /          \ Unit Tests
   /────────────\
```

**Test Types:**
- **Unit Tests** - Models, Services
- **Feature Tests** - Controllers, Routes
- **Browser Tests** - Critical user flows (Dusk)

---

## 📈 Scalability Considerations

### Horizontal Scaling
- Stateless application servers
- Redis for session storage
- Database replication (read replicas)
- CDN for static assets

### Vertical Scaling
- Database query optimization
- Eager loading relationships
- Index optimization
- Query caching

---

## 📝 Coding Standards

- **PSR-12** - PHP coding standard
- **BEM** - CSS naming convention (optional)
- **RESTful** - API design (future)
- **SOLID** - Object-oriented principles

---

**This architecture supports the long-term growth and scalability of KABZS EVENT.**

