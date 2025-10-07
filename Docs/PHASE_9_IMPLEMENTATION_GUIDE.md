# 🚀 KABZS EVENT - Phase 9 Implementation Guide

**Phase:** Vendor Verification + Subscription Plans (Ghana Edition)  
**Status:** ✅ Migrations Complete - Ready for Implementation  
**Database:** event_management_db  

---

## ✅ What's Been Done So Far

### Migrations Created & Executed (2 tables)
1. ✅ **verification_requests** table created
2. ✅ **vendor_subscriptions** table created

**Status:** Both tables now exist in database!

---

## 📊 Database Schema

### verification_requests Table
```sql
id                  bigint (PK)
vendor_id           bigint (FK → vendors.id)
id_document_path    string (nullable)
social_links        json (nullable)
status              enum('pending','approved','rejected') DEFAULT 'pending'
admin_note          text (nullable)
submitted_at        timestamp (nullable)
decided_at          timestamp (nullable)
created_at          timestamp
updated_at          timestamp

Indexes: vendor_id, status
```

### vendor_subscriptions Table
```sql
id                  bigint (PK)
vendor_id           bigint (FK → vendors.id)
plan                string
price_amount        decimal(10,2)
currency            string DEFAULT 'GHS'
status              enum('active','expired','cancelled') DEFAULT 'active'
started_at          timestamp
ends_at             timestamp (nullable)
payment_reference   string (nullable)
created_at          timestamp
updated_at          timestamp

Indexes: vendor_id, status, ends_at
```

---

## 🎯 Phase 9 Remaining Tasks

This phase has been **partially completed**. Here's what still needs to be built:

### Part A: Vendor Verification System

**1. Models to Update:**
```php
// app/Models/VerificationRequest.php
- Add fillable fields
- Add casts (status, submitted_at, decided_at, social_links)
- Add relationship to Vendor

// app/Models/Vendor.php  
- Add relationship: hasOne(VerificationRequest::class)
```

**2. Controllers to Create:**
```bash
php artisan make:controller Vendor/VerificationController
php artisan make:controller Admin/VendorVerificationController
```

**3. Views to Create:**
- `resources/views/vendor/verification.blade.php` - Verification request form
- `resources/views/admin/verifications/index.blade.php` - Admin approval interface

**4. Routes to Add:**
```php
// Vendor routes
Route::get('vendor/verification', ...)->name('vendor.verification');
Route::post('vendor/verification', ...)->name('vendor.verification.store');

// Admin routes
Route::get('admin/verifications', ...)->name('admin.verifications.index');
Route::post('admin/verifications/{id}/approve', ...)->name('admin.verifications.approve');
Route::post('admin/verifications/{id}/reject', ...)->name('admin.verifications.reject');
```

---

### Part B: Subscription Plans System

**1. Models to Update:**
```php
// app/Models/VendorSubscription.php
- Add fillable fields
- Add casts (price_amount, started_at, ends_at, status)
- Add relationship to Vendor

// app/Models/Vendor.php
- Add relationship: hasMany(VendorSubscription::class)
- Add method: activeSubscription()
```

**2. Seeder to Create:**
```bash
php artisan make:seeder SubscriptionPlanSeeder
```

**Plans to Seed:**
- Free Plan: GH₵ 0.00 (Lifetime)
- Premium Plan: GH₵ 99.00 (30 days)
- Gold Plan: GH₵ 249.00 (90 days)

**3. Controllers to Create:**
```bash
php artisan make:controller Vendor/SubscriptionController
```

**4. Views to Create:**
- `resources/views/vendor/subscriptions/index.blade.php` - Plan selection
- `resources/views/vendor/subscriptions/success.blade.php` - Payment success

**5. Routes to Add:**
```php
Route::get('vendor/subscriptions', ...)->name('vendor.subscriptions');
Route::post('vendor/subscriptions/{plan}', ...)->name('vendor.subscriptions.subscribe');
```

---

## 💰 Subscription Plans (Ghana Context)

### Free Plan
- **Price:** GH₵ 0.00
- **Duration:** Lifetime
- **Features:**
  - Basic vendor listing
  - Up to 5 services
  - Basic profile page
  - Standard search visibility

### Premium Plan
- **Price:** GH₵ 99.00 / month
- **Duration:** 30 days
- **Features:**
  - Highlighted vendor card (gold border)
  - Top of category listings
  - Up to 15 services
  - Priority in search results
  - Badge: "Premium Vendor"

### Gold Plan
- **Price:** GH₵ 249.00 / 3 months
- **Duration:** 90 days
- **Features:**
  - Featured on homepage
  - Top position in all listings
  - Unlimited services
  - Verified badge
  - Analytics dashboard
  - Badge: "Gold Vendor ⭐"
  - Priority support

---

## 🔐 Verification Workflow

### Vendor Side
```
1. Vendor logs into dashboard
2. Clicks "Request Verification" button
3. Fills form:
   - Upload ID document (Ghana Card / Passport)
   - Optional: Facebook, Instagram links
4. Submits request
5. Status: "Pending Verification"
6. Wait for admin approval
```

### Admin Side
```
1. Admin logs into admin panel
2. Sees list of pending verifications
3. Views vendor details + document
4. Clicks "Approve" or "Reject"
5. If reject: adds admin note (reason)
6. Vendor's is_verified flag updates
7. Vendor receives notification (future)
```

---

## 💳 Payment Integration (Ghana)

### Recommended: Paystack
**Why Paystack:**
- Most popular in Ghana
- Supports Mobile Money (MTN, Vodafone Cash, AirtelTigo Money)
- Supports Visa/Mastercard
- GHS transactions
- Easy API integration

**Test Keys Available:**
```env
PAYSTACK_PUBLIC_KEY=pk_test_xxx
PAYSTACK_SECRET_KEY=sk_test_xxx
```

### Alternative: Flutterwave
- Also supports Ghana
- Mobile Money integration
- GHS support

### For Development
- Use test mode
- Simulate payments
- Mark subscription active manually
- Production: Real payment gateway

---

## 🎨 UI Components to Use

### Verification Form
- `<x-card>` - Form container
- `<x-input-label>` - Field labels
- `<x-button variant="primary">` - Submit button
- `<x-alert>` - Success/error messages

### Admin Verification Table
- `<x-card>` - Table container
- `<x-button variant="primary">` - Approve button
- `<x-button variant="danger">` - Reject button
- `<x-badge type="warning">` - Pending status
- `<x-badge type="success">` - Approved status

### Subscription Plans
- `<x-card>` - Plan cards
- `<x-button variant="accent">` - Subscribe button
- Price display: **GH₵ 99.00 / month**
- Features list with checkmarks

---

## 📝 Next Steps to Complete Phase 9

### Step 1: Update Models
Add relationships and fillable fields to:
- VerificationRequest.php
- VendorSubscription.php  
- Vendor.php (add relationships)

### Step 2: Create Controllers
Generate and implement:
- Vendor/VerificationController
- Admin/VendorVerificationController
- Vendor/SubscriptionController

### Step 3: Create Seeders
- SubscriptionPlanSeeder (3 plans in GHS)

### Step 4: Create Views
- vendor/verification.blade.php
- admin/verifications/index.blade.php
- vendor/subscriptions/index.blade.php

### Step 5: Add Routes
Update routes/web.php with vendor and admin routes

### Step 6: Test
- Request verification as vendor
- Approve as admin
- Subscribe to plan
- Verify subscription active

---

## 🎯 Expected Features After Phase 9

### For Vendors
✅ Request verification with document upload  
✅ Choose subscription plan  
✅ Pay with Paystack (test mode)  
✅ See subscription status on dashboard  
✅ Get verified badge after approval  

### For Admins
✅ View pending verifications  
✅ Approve/reject with notes  
✅ See all subscriptions  
✅ Moderate vendor access  

### For Clients
✅ See verified vendors (more trust)  
✅ See premium/gold badges on vendors  
✅ Better quality vendor directory  

---

## 🎊 Current Status

**Migrations:** ✅ Complete (2 new tables created)  
**Models:** ⏳ Need to be updated  
**Controllers:** ⏳ Need to be created  
**Views:** ⏳ Need to be created  
**Routes:** ⏳ Need to be added  
**Seeders:** ⏳ Need to be created  

**Ready for:** Full Phase 9 implementation

---

## 📚 Ghana Context

**Currency:** GH₵ (Ghana Cedis)  
**Payment:** Paystack / Flutterwave  
**Mobile Money:** MTN, Vodafone Cash supported  
**Pricing:** Competitive Ghana rates  
**Language:** Ghana-appropriate terminology  

---

**Status:** Migrations complete, ready for full implementation  
**Progress:** 80% overall, Phase 9 at 20%  
**Next:** Complete Part A (Verification) and Part B (Subscriptions)

