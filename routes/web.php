<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicVendorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\LocationController;
use App\Http\Controllers\SuperAdmin\SMSTestController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardControllerNew;
use App\Http\Controllers\Vendor\ServiceController;
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\VerificationController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\VendorRegistrationController;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/load-more-vendors', [HomeController::class, 'loadMoreVendors'])->name('home.load-more-vendors');

// Search & Filter Vendors
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/advanced', [\App\Http\Controllers\AdvancedSearchController::class, 'index'])->name('search.advanced');

// Location and Nearby Vendors (AJAX endpoints)
Route::post('/api/location/update', [\App\Http\Controllers\AdvancedSearchController::class, 'updateLocation'])
    ->middleware('auth')
    ->name('api.location.update');
Route::get('/api/vendors/nearby', [\App\Http\Controllers\AdvancedSearchController::class, 'getNearbyVendors'])
    ->name('api.vendors.nearby');

// Live Search API (AJAX endpoint)
Route::get('/api/search/live', [SearchController::class, 'liveSearch'])
    ->name('api.search.live');

// Public Vendor Registration (for non-logged-in users)
Route::get('/signup/vendor', [PublicVendorController::class, 'create'])->name('vendor.public.register');
Route::post('/signup/vendor', [PublicVendorController::class, 'store'])->name('vendor.public.store');

// Public Vendor Profiles (no auth required)
Route::get('/vendors', [VendorProfileController::class, 'index'])->name('vendors.index');
Route::get('/vendors/{slug}', [VendorProfileController::class, 'show'])->name('vendors.show');

// Log vendor view for recommendations
Route::post('/vendors/{vendor}/log-view', function (\App\Models\Vendor $vendor) {
    $user = auth()->user();
    if ($user) {
        \App\Services\PersonalizedSearchService::logVendorView($user, $vendor);
    }
    return response()->json(['success' => true]);
})->name('vendors.log-view');

// Recommendation API (public - returns JSON)
Route::get('/recommendations', function (Request $request) {
    $lat = $request->query('lat');
    $lng = $request->query('lng');
    $category = $request->query('category_id');
    $limit = intval($request->query('limit', 8));
    
    $recs = RecommendationService::get([
        'lat' => $lat,
        'lng' => $lng,
        'category_id' => $category,
        'limit' => $limit,
    ]);
    
    return response()->json($recs);
})->name('recommendations');

/*
|--------------------------------------------------------------------------
| Unified Dashboard Routes (Phase K5)
|--------------------------------------------------------------------------
| All dashboard routes consolidated under /dashboard prefix
| Role-based middleware ensures proper access control
|
*/

// Main Dashboard Route - Auto-redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// All dashboard sub-routes under unified /dashboard prefix
Route::prefix('dashboard')->middleware(['auth'])->group(function () {

    // Super Admin Routes
    Route::middleware(['role:super_admin'])->name('superadmin.')->group(function () {
        // Configuration Center (Phase K6)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('index');
            
            Route::get('/paystack', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'paystack'])->name('paystack');
            Route::post('/paystack', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updatePaystack'])->name('paystack.update');
            
            Route::get('/cloudinary', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'cloudinary'])->name('cloudinary');
            Route::post('/cloudinary', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateCloudinary'])->name('cloudinary.update');
            
            Route::get('/sms', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'sms'])->name('sms');
            Route::post('/sms', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSms'])->name('sms.update');
            
            Route::get('/smtp', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'smtp'])->name('smtp');
            Route::post('/smtp', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSmtp'])->name('smtp.update');
            
            Route::get('/system', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'system'])->name('system');
            Route::post('/system', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSystem'])->name('system.update');
        });
        
        // SMS Test Interface
        Route::get('/sms-test', [SMSTestController::class, 'index'])->name('sms.test');
        Route::post('/sms-test', [SMSTestController::class, 'send'])->name('sms.test.send');
        
        // Backup Management
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{id}', [BackupController::class, 'destroy'])->name('backups.destroy');
        
        // Location Management & CSV Import
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
        Route::get('/locations/upload', [LocationController::class, 'uploadForm'])->name('locations.upload');
        Route::post('/locations/import', [LocationController::class, 'importCsv'])->name('locations.import');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->name('admin.')->group(function () {
        // Vendor Verification Management
        Route::get('/verifications', [VendorVerificationController::class, 'index'])->name('verifications.index');
        Route::post('/verifications/{id}/approve', [VendorVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{id}/reject', [VendorVerificationController::class, 'reject'])->name('verifications.reject');
        Route::post('/verifications/{vendorId}/suspend', [VendorVerificationController::class, 'suspend'])->name('verifications.suspend');
        Route::post('/verifications/{vendorId}/cancel', [VendorVerificationController::class, 'cancelVerification'])->name('verifications.cancel');
        
        // Client Management
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
        Route::post('/clients/{id}/deactivate', [ClientController::class, 'deactivate'])->name('clients.deactivate');
        Route::post('/clients/{id}/activate', [ClientController::class, 'activate'])->name('clients.activate');
        Route::post('/clients/{id}/reset-password', [ClientController::class, 'resetPassword'])->name('clients.resetPassword');
        
        // Reports & Issues Management (Phase L1)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('reports.update');
        Route::post('/reports/{id}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/{id}/reopen', [ReportController::class, 'reopen'])->name('reports.reopen');
        
        // User Management
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    });

    // Vendor Routes
    Route::middleware(['role:vendor'])->name('vendor.')->group(function () {
        // Vendor Business Profile
        Route::get('/business-profile', [\App\Http\Controllers\Vendor\ProfileController::class, 'index'])->name('profile');
        Route::get('/business-profile/edit', [\App\Http\Controllers\Vendor\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/business-profile', [\App\Http\Controllers\Vendor\ProfileController::class, 'update'])->name('profile.update');
        
        // Service Management (CRUD)
        Route::resource('services', ServiceController::class);
        
        // Verification Request
        Route::get('/verification', [VerificationController::class, 'index'])->name('verification');
        Route::post('/verification', [VerificationController::class, 'store'])->name('verification.store');
        
        // Subscription Plans
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
        Route::post('/subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    });

    // Client Routes
    // Note: Client dashboard is served via main /dashboard route
    Route::middleware(['role:client'])->name('client.')->group(function () {
        // Client-specific routes can be added here
    });

    // ============================================================
    // Common Dashboard Routes (All Authenticated Users)
    // ============================================================
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Vendor Registration (for existing users to upgrade to vendor)
    Route::get('/vendor-register', [VendorRegistrationController::class, 'create'])->name('vendor.register');
    Route::post('/vendor-register', [VendorRegistrationController::class, 'store'])->name('vendor.store');
    
    // Message System (Phase K: Chat with Rate Limiting)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/conversation', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::middleware('message.rate')->group(function () {
        Route::post('/messages/send', [MessageController::class, 'store'])->name('messages.store');
    });
    
    // Notifications (Phase K2)
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Report Issue (Phase L1) - Rate Limited to 5 per minute
    Route::middleware('throttle:5,1')->group(function () {
        Route::get('/report', [\App\Http\Controllers\ReportController::class, 'create'])->name('report.create');
        Route::post('/report', [\App\Http\Controllers\ReportController::class, 'store'])->name('report.store');
    });
});

// ============================================================
// Public/API Routes (Outside Dashboard)
// ============================================================
Route::middleware('auth')->group(function () {
    // Report Vendor (can be done from public vendor pages) - Legacy route maintained for compatibility
    Route::post('/reports', [App\Http\Controllers\Admin\ReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/vendor', [App\Http\Controllers\Admin\ReportController::class, 'store'])->name('reports.vendor.store');
    
    // Review Submission (authenticated users only)
    Route::post('/vendors/{vendor}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // User Interaction Logging (with rate limiting)
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/interactions/vendors/{vendor}/view', [InteractionController::class, 'logView'])->name('interactions.view');
        Route::post('/interactions/search', [InteractionController::class, 'logSearch'])->name('interactions.search');
        Route::post('/interactions/category', [InteractionController::class, 'logCategoryView'])->name('interactions.category');
        Route::post('/interactions/vendors/{vendor}/recommendation-click', [InteractionController::class, 'logRecommendationClick'])->name('interactions.recommendation');
        Route::post('/interactions/log', [InteractionController::class, 'logAction'])->name('interactions.log');
    });

    // Personalized Recommendations (authenticated users only)
    Route::get('/personalized/recommendations', function (Request $request) {
        $user = auth()->user();
        $lat = $request->query('lat') ?? ($user->preferences->last_lat ?? null);
        $lng = $request->query('lng') ?? ($user->preferences->last_lng ?? null);
        $limit = intval($request->query('limit', 8));
        $category = $request->query('category_id');

        $recs = \App\Services\PersonalRecommendationService::get([
            'user_id' => $user->id,
            'lat' => $lat,
            'lng' => $lng,
            'category_id' => $category,
            'limit' => $limit,
        ]);

        return response()->json($recs);
    })->name('personal.recommendations');
});

require __DIR__.'/auth.php';
