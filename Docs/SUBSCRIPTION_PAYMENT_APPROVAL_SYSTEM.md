# 🚀 Subscription Payment & Approval Workflow System

## Overview
Complete implementation of vendor-initiated subscription purchases with Paystack payment integration, admin approval workflow, and automatic approval system.

---

## ✅ COMPLETED FEATURES

### 1. **Database Schema**
Created comprehensive database structure:

- **vendor_subscriptions table** - Added columns:
  - `payment_status` (pending/paid/failed)
  - `payment_method` (paystack/manual)
  - `paystack_reference` (unique payment ID)
  - `paid_at` (timestamp)
  - `approval_status` (pending/approved/rejected)
  - `approved_by` (admin user ID)
  - `approved_at` (timestamp)
  - `approval_note` (admin feedback)
  - `payment_expires_at` (auto-approval deadline)

- **featured_ads table** - Same payment/approval columns

- **subscription_payments table** - Payment tracking:
  - `vendor_subscription_id`
  - `paystack_reference`
  - `payment_status`
  - `amount`, `currency`
  - `payment_channel` (card, mobile_money, bank)
  - `customer_email`
  - `metadata` (JSON from Paystack)
  - `paid_at`

- **admin_settings table** - System configuration:
  - `key`, `value`, `type`, `description`, `group`
  - Pre-populated with auto-approval settings
  - Paystack API keys storage

### 2. **Models**

**VendorSubscription Model:**
- `isPaid()` - Check if payment completed
- `isApproved()` - Check if admin approved
- `isPendingApproval()` - Check if waiting for review
- `shouldAutoApprove()` - Check if auto-approval should trigger
- `approve($adminId, $note)` - Approve subscription
- `reject($adminId, $note)` - Reject subscription
- Relationships: `vendor()`, `approver()`, `payments()`

**FeaturedAd Model:**
- Similar methods for payment/approval workflow
- `isPaid()`, `isApproved()`, `isPendingApproval()`
- `approve()`, `reject()`

**SubscriptionPayment Model:**
- Tracks individual payment transactions
- `isSuccessful()`, `markAsPaid()`, `markAsFailed()`
- Stores Paystack metadata

**AdminSetting Model:**
- `AdminSetting::getValue($key, $default)` - Get typed value
- `AdminSetting::set($key, $value, $type)` - Set value
- `AdminSetting::getGroup($group)` - Get all settings in group
- Auto type-casting (boolean, integer, json)

### 3. **Services**

**PaystackService:**
- `initializePayment()` - Create payment transaction
- `verifyPayment()` - Verify callback
- `generateReference()` - Unique payment IDs
- Reads API keys from AdminSetting or .env fallback

### 4. **Controllers**

**SubscriptionPaymentController (Vendor):**
- `initiatePayment($plan)` - Start payment flow
- Creates subscription record
- Generates Paystack payment link
- Redirects vendor to Paystack

**PaystackCallbackController:**
- `handleCallback()` - Process Paystack webhook
- Verifies payment with Paystack API
- Updates subscription/ad payment status
- Sets auto-approval deadline (24 hours)
- Notifies admins

**SubscriptionApprovalController (Admin):**
- `pendingSubscriptions()` - List pending subscriptions
- `pendingFeaturedAds()` - List pending ads
- `approveSubscription($id)` - Manually approve
- `rejectSubscription($id)` - Manually reject
- Sends notifications to vendors

**AdminSettingsController:**
- `subscriptionSettings()` - Show auto-approval config
- `updateSubscriptionSettings()` - Save config
- `paymentSettings()` - Manage Paystack keys

### 5. **Notifications**

**SubscriptionApprovedNotification:**
- Sent when subscription approved (manual or auto)
- Email + Database notification
- Includes subscription details

**FeaturedAdApprovedNotification:**
- Sent when ad approved
- Email + Database notification

**NewSubscriptionPendingNotification:**
- Sent to all admins when payment completed
- Alerts to review within 24 hours
- Includes vendor & payment details

### 6. **Background Jobs**

**AutoApproveSubscriptions (Hourly):**
- Runs every hour via Laravel Scheduler
- Checks `subscription_auto_approval_enabled` setting
- Auto-approves subscriptions past `payment_expires_at`
- Auto-approves featured ads past deadline
- Sends approval notifications
- Logs all actions

### 7. **Views**

**vendor/subscriptions/status.blade.php:**
- Shows payment status (pending/paid/failed)
- Shows approval status (pending/approved/rejected)
- Shows subscription status (active/inactive)
- Visual timeline/progress indicator
- Auto-approval countdown timer
- Full subscription details

**admin/subscriptions/pending.blade.php:**
- Table of all pending subscriptions
- Shows vendor, plan, amount, paid date
- Auto-approval countdown
- Approve/Reject modals
- Pagination support

**admin/settings/subscriptions.blade.php:**
- Toggle auto-approval on/off
- Set auto-approval wait time (1-168 hours)
- Separate settings for subscriptions & ads
- Informational guide

### 8. **Routes**

**Vendor Routes:**
```php
POST /dashboard/subscriptions/{plan} - Initiate payment
GET /dashboard/subscriptions/{id}/status - Track status
```

**Admin Routes:**
```php
GET /dashboard/admin/subscriptions/pending - List pending
POST /dashboard/admin/subscriptions/{id}/approve - Approve
POST /dashboard/admin/subscriptions/{id}/reject - Reject
GET /dashboard/admin/settings/subscriptions - Configure auto-approval
POST /dashboard/admin/settings/subscriptions - Save config
```

**Webhook Routes:**
```php
GET /paystack/callback - Payment verification
```

### 9. **Scheduler**

**app/Console/Kernel.php:**
- AutoApproveSubscriptions runs every hour
- Checks for expired approval deadlines
- Auto-approves if enabled in settings

---

## 🔧 CONFIGURATION REQUIRED

### 1. **Paystack API Keys**
Admin must configure in:
- `/dashboard/admin/settings/payments`
- Or add to `.env`:
  ```
  PAYSTACK_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
  PAYSTACK_SECRET_KEY=sk_test_xxxxxxxxxxxxx
  ```

### 2. **Auto-Approval Settings**
Configure at: `/dashboard/admin/settings/subscriptions`

Default settings:
- ✅ Auto-approval: Enabled
- ✅ Wait time: 24 hours
- ✅ Applies to subscriptions & featured ads

### 3. **Laravel Scheduler**
Must be running for auto-approval:

**On Production Server:**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

**On Local (Windows):**
```powershell
php artisan schedule:work
```

---

## 📋 WORKFLOW

### Vendor Flow:
1. ✅ Visits `/dashboard/subscriptions`
2. ✅ Clicks "Subscribe Now" on Premium/Gold plan
3. ✅ System creates subscription record (status: pending)
4. ✅ Redirects to Paystack payment page
5. ✅ Vendor completes payment
6. ✅ Paystack redirects to `/paystack/callback`
7. ✅ System verifies payment → Updates to "Under Review"
8. ✅ Vendor sees status at `/subscriptions/{id}/status`
9. ✅ Shows countdown: "Auto-approval in 24 hours"
10. ✅ Gets notification when approved

### Admin Flow:
1. ✅ Receives email: "New Subscription Pending"
2. ✅ Sees bell notification count increase
3. ✅ Visits `/dashboard/admin/subscriptions/pending`
4. ✅ Reviews vendor details, payment info
5. ✅ Clicks "Approve" → Adds optional note
6. ✅ OR Clicks "Reject" → Must provide reason
7. ✅ Vendor gets instant notification

### Auto-Approval Flow:
1. ✅ AutoApproveSubscriptions job runs hourly
2. ✅ Finds subscriptions past payment_expires_at
3. ✅ Checks if auto_approval_enabled = true
4. ✅ Approves subscription automatically
5. ✅ Vendor gets approval notification
6. ✅ Subscription activated immediately

---

## 🎨 WHAT STILL NEEDS WORK

### 1. **Admin Dashboard Widget**
Add pending count to admin dashboard:
- Show count of pending subscriptions
- Show count of pending featured ads
- Quick link to approval pages

### 2. **Vendor Dashboard Widget**
Add subscription status to vendor dashboard:
- Show current plan badge
- Show "Under Review" notification
- Link to status page

### 3. **Free Plan Auto-Assignment**
Update VendorRegistrationController & PublicVendorController:
- Set payment_status = 'paid' for Free plan
- Set approval_status = 'approved' for Free plan
- No payment_expires_at for Free plan

### 4. **Email Templates**
Create proper HTML email templates for:
- Subscription approved notification
- Featured ad approved notification
- Admin pending review notification

### 5. **Testing**
- ✅ Test Paystack payment flow
- ✅ Test callback verification
- ✅ Test admin approval
- ✅ Test auto-approval job
- ✅ Test notifications (email + database)

### 6. **Payment Receipts**
- Generate PDF receipts for payments
- Store in subscription_payments metadata
- Allow vendor to download

### 7. **Refund System**
- Admin can issue refunds for rejected subscriptions
- Integrate with Paystack refund API

---

## 🔐 SECURITY FEATURES

✅ Paystack reference verification prevents fraud
✅ Admin-only approval routes with role middleware
✅ CSRF protection on all forms
✅ Payment metadata validation
✅ Secure API key storage in database
✅ Audit trail (approved_by, approved_at, approval_note)

---

## 📊 ADMIN SETTINGS

**Subscription Auto-Approval:**
- `subscription_auto_approval_enabled` (boolean)
- `subscription_auto_approval_hours` (1-168)

**Featured Ad Auto-Approval:**
- `featured_ad_auto_approval_enabled` (boolean)
- `featured_ad_auto_approval_hours` (1-168)

**Payment Gateway:**
- `paystack_public_key`
- `paystack_secret_key`

---

## 🎯 NEXT STEPS FOR FULL PRODUCTION

1. **Configure Paystack Keys** in admin settings
2. **Test payment flow** with test keys
3. **Configure auto-approval** times as needed
4. **Set up Laravel Scheduler** on server
5. **Test email notifications** (configure SMTP)
6. **Update vendor dashboard** to show subscription status
7. **Create admin dashboard widgets** for quick access
8. **Test full end-to-end flow**

---

## 📞 SUPPORT

For questions or issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Check Paystack dashboard for payment status
- Review admin_settings table for configuration
- Check scheduled jobs: `php artisan schedule:list`

---

**Status:** ✅ Core System Complete | ⏳ Testing & Polish Needed

**Estimated Remaining Work:** 2-3 hours for testing, dashboard widgets, and email templates

