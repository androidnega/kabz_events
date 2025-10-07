# 📋 KABZS EVENT - Phase 9 Remaining Work (60%)

**Current Status:** 40% Complete  
**Remaining:** 60% (estimated 2-3 hours of work)  
**Focus:** Vendor Verification + Subscription Plans  

---

## ✅ What's Already Done (40%)

### Completed:
1. ✅ **Database Migrations** (2 tables created)
   - `verification_requests` table
   - `vendor_subscriptions` table

2. ✅ **Models Created**
   - `VerificationRequest` model exists
   - `VendorSubscription` model exists

3. ✅ **Controllers Generated**
   - `Vendor/VerificationController` (empty)
   - `Admin/VendorVerificationController` (empty)
   - `Vendor/SubscriptionController` (empty)

4. ✅ **Vendor Model Updated**
   - Added `verificationRequest()` relationship
   - Added `subscriptions()` relationship
   - Added `activeSubscription()` method

---

## ⏳ What's Remaining (60%)

### **Part A: Vendor Verification System (30%)**

#### 1. Complete VerificationRequest Model ⏳
**File:** `app/Models/VerificationRequest.php`

**What to Add:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'id_document_path',
        'social_links',
        'status',
        'admin_note',
        'submitted_at',
        'decided_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
```

**Estimated Time:** 5 minutes

---

#### 2. Implement Vendor/VerificationController ⏳
**File:** `app/Http/Controllers/Vendor/VerificationController.php`

**Methods Needed:**
- `index()` - Show verification form
- `store()` - Submit verification request

**Key Logic:**
```php
public function index()
{
    $vendor = auth()->user()->vendor;
    $request = $vendor->verificationRequest;
    return view('vendor.verification', compact('vendor', 'request'));
}

public function store(Request $request)
{
    // Validate
    $request->validate([
        'id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'social_links.facebook' => 'nullable|url',
        'social_links.instagram' => 'nullable|url',
    ]);

    // Upload document
    $path = $request->file('id_document')->store('verification-docs', 'public');

    // Create verification request
    VerificationRequest::create([
        'vendor_id' => auth()->user()->vendor->id,
        'id_document_path' => $path,
        'social_links' => $request->social_links,
        'status' => 'pending',
        'submitted_at' => now(),
    ]);

    return back()->with('success', 'Verification request submitted successfully!');
}
```

**Estimated Time:** 20 minutes

---

#### 3. Implement Admin/VendorVerificationController ⏳
**File:** `app/Http/Controllers/Admin/VendorVerificationController.php`

**Methods Needed:**
- `index()` - List all verification requests
- `approve($id)` - Approve verification
- `reject($id)` - Reject verification

**Key Logic:**
```php
public function index()
{
    $requests = VerificationRequest::with('vendor')
        ->orderBy('status')
        ->orderBy('submitted_at', 'desc')
        ->paginate(20);
    
    return view('admin.verifications.index', compact('requests'));
}

public function approve($id)
{
    $verification = VerificationRequest::findOrFail($id);
    
    $verification->update([
        'status' => 'approved',
        'decided_at' => now(),
    ]);

    $verification->vendor->update([
        'is_verified' => true,
        'verified_at' => now(),
    ]);

    return back()->with('success', 'Vendor verified successfully!');
}

public function reject(Request $request, $id)
{
    $request->validate(['admin_note' => 'required|string|max:500']);
    
    $verification = VerificationRequest::findOrFail($id);
    
    $verification->update([
        'status' => 'rejected',
        'admin_note' => $request->admin_note,
        'decided_at' => now(),
    ]);

    return back()->with('success', 'Verification rejected.');
}
```

**Estimated Time:** 25 minutes

---

#### 4. Create Vendor Verification View ⏳
**File:** `resources/views/vendor/verification.blade.php`

**What to Build:**
- Form to upload ID document
- Fields for Facebook/Instagram links
- Display current verification status
- Show admin note if rejected

**Estimated Time:** 20 minutes

---

#### 5. Create Admin Verification View ⏳
**File:** `resources/views/admin/verifications/index.blade.php`

**What to Build:**
- Table of all verification requests
- Columns: Vendor name, status, document link, actions
- Filter by status (pending, approved, rejected)
- Approve/Reject buttons with modal for notes

**Estimated Time:** 25 minutes

---

#### 6. Add Verification Routes ⏳
**File:** `routes/web.php`

**Routes to Add:**
```php
// Vendor verification routes
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('verification', [VerificationController::class, 'index'])->name('verification');
    Route::post('verification', [VerificationController::class, 'store'])->name('verification.store');
});

// Admin verification routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('verifications', [VendorVerificationController::class, 'index'])->name('verifications.index');
    Route::post('verifications/{id}/approve', [VendorVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('verifications/{id}/reject', [VendorVerificationController::class, 'reject'])->name('verifications.reject');
});
```

**Estimated Time:** 5 minutes

---

### **Part B: Subscription Plans System (30%)**

#### 1. Complete VendorSubscription Model ⏳
**File:** `app/Models/VendorSubscription.php`

**What to Add:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'plan',
        'price_amount',
        'currency',
        'status',
        'started_at',
        'ends_at',
        'payment_reference',
    ];

    protected $casts = [
        'price_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
```

**Estimated Time:** 10 minutes

---

#### 2. Create Subscription Plan Seeder ⏳
**File:** `database/seeders/SubscriptionPlanSeeder.php`

**What to Seed:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Note: This is just for reference
        // Actual plans stored in config or database table
        
        $plans = [
            [
                'name' => 'Free',
                'price' => 0.00,
                'currency' => 'GHS',
                'duration_days' => null, // Lifetime
                'features' => [
                    'Basic vendor listing',
                    'Up to 5 services',
                    'Basic profile page',
                    'Standard search visibility',
                ],
            ],
            [
                'name' => 'Premium',
                'price' => 99.00,
                'currency' => 'GHS',
                'duration_days' => 30,
                'features' => [
                    'Highlighted vendor card',
                    'Top of category listings',
                    'Up to 15 services',
                    'Priority in search results',
                    'Premium badge',
                ],
            ],
            [
                'name' => 'Gold',
                'price' => 249.00,
                'currency' => 'GHS',
                'duration_days' => 90,
                'features' => [
                    'Featured on homepage',
                    'Top position in all listings',
                    'Unlimited services',
                    'Verified badge',
                    'Analytics dashboard',
                    'Gold badge ⭐',
                    'Priority support',
                ],
            ],
        ];

        // Store in config or separate plans table
    }
}
```

**Estimated Time:** 10 minutes

---

#### 3. Implement Vendor/SubscriptionController ⏳
**File:** `app/Http/Controllers/Vendor/SubscriptionController.php`

**Methods Needed:**
- `index()` - Show available plans
- `subscribe($plan)` - Handle subscription

**Key Logic:**
```php
public function index()
{
    $vendor = auth()->user()->vendor;
    $currentSubscription = $vendor->activeSubscription();
    
    $plans = [
        'free' => ['name' => 'Free', 'price' => 0, 'duration' => 'Lifetime'],
        'premium' => ['name' => 'Premium', 'price' => 99, 'duration' => '30 days'],
        'gold' => ['name' => 'Gold', 'price' => 249, 'duration' => '90 days'],
    ];

    return view('vendor.subscriptions.index', compact('plans', 'currentSubscription'));
}

public function subscribe(Request $request, $plan)
{
    $vendor = auth()->user()->vendor;
    
    $plans = [
        'free' => ['price' => 0, 'days' => null],
        'premium' => ['price' => 99, 'days' => 30],
        'gold' => ['price' => 249, 'days' => 90],
    ];

    if (!isset($plans[$plan])) {
        return back()->with('error', 'Invalid plan selected.');
    }

    $planData = $plans[$plan];

    // For free plan, activate immediately
    if ($plan === 'free') {
        VendorSubscription::create([
            'vendor_id' => $vendor->id,
            'plan' => 'Free',
            'price_amount' => 0,
            'currency' => 'GHS',
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => null,
        ]);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Free plan activated!');
    }

    // For paid plans, simulate Paystack payment
    // In production: redirect to Paystack checkout
    
    // For now, mark as active (test mode)
    VendorSubscription::create([
        'vendor_id' => $vendor->id,
        'plan' => ucfirst($plan),
        'price_amount' => $planData['price'],
        'currency' => 'GHS',
        'status' => 'active',
        'started_at' => now(),
        'ends_at' => now()->addDays($planData['days']),
        'payment_reference' => 'TEST_' . strtoupper(uniqid()),
    ]);

    return redirect()->route('vendor.dashboard')
        ->with('success', ucfirst($plan) . ' plan activated! (Test Mode)');
}
```

**Estimated Time:** 25 minutes

---

#### 4. Create Subscription Plans View ⏳
**File:** `resources/views/vendor/subscriptions/index.blade.php`

**What to Build:**
- 3 plan cards (Free, Premium, Gold)
- Display price in GH₵
- Feature list for each plan
- "Subscribe" buttons
- Highlight current plan
- Show expiry date if subscribed

**Estimated Time:** 30 minutes

---

#### 5. Add Subscription Routes ⏳
**File:** `routes/web.php`

**Routes to Add:**
```php
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
});
```

**Estimated Time:** 5 minutes

---

#### 6. Update Vendor Dashboard ⏳
**File:** `resources/views/vendor/dashboard.blade.php`

**What to Add:**
- Display current subscription status
- Show expiry date
- "Upgrade" button if on Free plan
- Verification status badge

**Estimated Time:** 15 minutes

---

## 📊 Time Breakdown Summary

### Part A: Verification System (30%)
| Task | Time |
|------|------|
| Complete VerificationRequest model | 5 min |
| Implement Vendor/VerificationController | 20 min |
| Implement Admin/VendorVerificationController | 25 min |
| Create vendor verification view | 20 min |
| Create admin verification view | 25 min |
| Add verification routes | 5 min |
| **Subtotal** | **~1.5 hours** |

### Part B: Subscription System (30%)
| Task | Time |
|------|------|
| Complete VendorSubscription model | 10 min |
| Create subscription seeder | 10 min |
| Implement Vendor/SubscriptionController | 25 min |
| Create subscription plans view | 30 min |
| Add subscription routes | 5 min |
| Update vendor dashboard | 15 min |
| **Subtotal** | **~1.5 hours** |

### **Total Estimated Time: 2.5-3 hours**

---

## 🎯 Checklist for Phase 9 Completion

### Models & Relationships
- [ ] Complete `VerificationRequest.php` (fillable, casts, relationships)
- [ ] Complete `VendorSubscription.php` (fillable, casts, relationships, methods)
- [ ] Verify `Vendor.php` relationships working

### Controllers
- [ ] Implement `Vendor/VerificationController` (2 methods)
- [ ] Implement `Admin/VendorVerificationController` (3 methods)
- [ ] Implement `Vendor/SubscriptionController` (2 methods)

### Views
- [ ] Create `vendor/verification.blade.php`
- [ ] Create `admin/verifications/index.blade.php`
- [ ] Create `vendor/subscriptions/index.blade.php`
- [ ] Update `vendor/dashboard.blade.php`

### Routes
- [ ] Add 3 vendor verification routes
- [ ] Add 3 admin verification routes
- [ ] Add 2 vendor subscription routes

### Seeders
- [ ] Create `SubscriptionPlanSeeder.php`
- [ ] Run seeder (optional)

### Testing
- [ ] Test verification request submission
- [ ] Test admin approval workflow
- [ ] Test admin rejection workflow
- [ ] Test Free plan subscription
- [ ] Test Premium plan subscription (test mode)
- [ ] Test Gold plan subscription (test mode)
- [ ] Test subscription display on dashboard
- [ ] Test verified badge display

---

## 🚀 Quick Implementation Order

**Recommended order to minimize context switching:**

### Session 1: Verification System (1.5 hours)
1. Complete `VerificationRequest` model (5 min)
2. Implement `Vendor/VerificationController` (20 min)
3. Create `vendor/verification.blade.php` (20 min)
4. Add vendor verification routes (5 min)
5. Test vendor verification submission (10 min)
6. Implement `Admin/VendorVerificationController` (25 min)
7. Create `admin/verifications/index.blade.php` (25 min)
8. Add admin verification routes (5 min)
9. Test admin approval/rejection (10 min)

### Session 2: Subscription System (1.5 hours)
1. Complete `VendorSubscription` model (10 min)
2. Create `SubscriptionPlanSeeder` (10 min)
3. Implement `Vendor/SubscriptionController` (25 min)
4. Create `vendor/subscriptions/index.blade.php` (30 min)
5. Add subscription routes (5 min)
6. Update `vendor/dashboard.blade.php` (15 min)
7. Test all 3 plan subscriptions (15 min)

---

## 💡 Pro Tips

### For Faster Development:
1. **Copy existing views** as templates (e.g., copy service create form)
2. **Reuse components** extensively (`<x-card>`, `<x-button>`, etc.)
3. **Use existing styling** from Phase 5 design system
4. **Test incrementally** - don't wait until everything is done

### Ghana-Specific Notes:
- Always display prices as **GH₵ 99.00**
- Use Ghana Card or Passport for verification
- Paystack test mode for development
- Mobile Money options in production

### Quality Checks:
- ✅ PSR-12 compliance
- ✅ Type hints on all methods
- ✅ Validation on all forms
- ✅ Flash messages for user feedback
- ✅ Mobile responsive design

---

## 🎊 What You'll Have After Phase 9

### For Vendors:
✅ Request verification with document upload  
✅ See verification status (pending/approved/rejected)  
✅ Choose subscription plan (Free/Premium/Gold)  
✅ See current subscription on dashboard  
✅ See expiry dates  
✅ Get verified badge after approval  

### For Admins:
✅ View all verification requests  
✅ Approve verifications (updates vendor status)  
✅ Reject with admin notes  
✅ Filter by verification status  
✅ See vendor documents  

### For Clients:
✅ See verified badges on vendors  
✅ See Premium/Gold badges  
✅ Trust system in place  
✅ Better quality vendor directory  

---

## 📈 Progress After Phase 9

**Current:** 85% overall (Phase 9 at 40%)  
**After Completion:** 92% overall (Phase 9 at 100%)  

**Remaining for 100%:**
- Phase 11: Advanced features (optional)
- Production deployment
- Payment gateway integration (Paystack live)
- Email notifications
- Final polish

---

## 🎯 Ready to Complete Phase 9?

**Estimated Time:** 2.5-3 hours  
**Difficulty:** Medium  
**Dependencies:** None (all foundations ready)  
**Impact:** High (adds trust + monetization)  

**Next Command:**
```bash
# Start with models
# Then controllers
# Then views
# Then routes
# Then test!
```

---

**Let's finish Phase 9 and get KABZS EVENT to 92% completion!** 🚀🇬🇭

