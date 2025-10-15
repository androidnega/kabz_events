<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FeaturedAdController as AdminFeaturedAdController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Admin\VipPlanController;
use App\Http\Controllers\Admin\VipSubscriptionController as AdminVipSubscriptionController;
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
use App\Http\Controllers\Vendor\FeaturedAdController;
use App\Http\Controllers\Vendor\ServiceController;
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\VerificationController;
use App\Http\Controllers\Vendor\VipSubscriptionController;
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
Route::get('/search/advanced', [\App\Http\Controllers\AdvancedSearchController::class, 'index'])->name('search.advanced');

// Site Mode Pages (Maintenance, Coming Soon, Update)
Route::get('/maintenance', function() {
    return view('site.maintenance');
})->name('site.maintenance');

Route::get('/coming-soon', function() {
    return view('site.coming-soon');
})->name('site.coming-soon');

Route::get('/update', function() {
    return view('site.update');
})->name('site.update');

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

    // Super Admin Routes (MUST be defined BEFORE client routes to take precedence)
    Route::middleware(['role:super_admin'])->name('superadmin.')->group(function () {
        // Configuration Center (Phase K6)
        Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings.index');
        
        Route::get('/settings/paystack', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'paystack'])->name('settings.paystack');
        Route::post('/settings/paystack', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updatePaystack'])->name('settings.paystack.update');
        
        Route::get('/settings/cloudinary', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'cloudinary'])->name('settings.cloudinary');
        Route::post('/settings/cloudinary', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateCloudinary'])->name('settings.cloudinary.update');
        
        Route::get('/settings/sms', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'sms'])->name('settings.sms');
        Route::post('/settings/sms', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSms'])->name('settings.sms.update');
        
        Route::get('/settings/smtp', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'smtp'])->name('settings.smtp');
        Route::post('/settings/smtp', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
        Route::post('/settings/smtp/test', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'testSmtp'])->name('settings.smtp.test');
        
        Route::get('/settings/system', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'system'])->name('settings.system');
        Route::post('/settings/system', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSystem'])->name('settings.system.update');
        
        Route::get('/settings/maintenance', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'maintenance'])->name('settings.maintenance');
        Route::post('/settings/maintenance', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateMaintenance'])->name('settings.maintenance.update');
        
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

    // Admin Routes (also accessible to super_admin)
    Route::middleware(['role:admin|super_admin'])->name('admin.')->group(function () {
        // Cloudinary Media Management
        Route::get('/media', [\App\Http\Controllers\Admin\CloudinaryMediaController::class, 'index'])->name('media.index');
        Route::get('/media/{folder}', [\App\Http\Controllers\Admin\CloudinaryMediaController::class, 'gallery'])->name('media.gallery');
        Route::get('/media/download', [\App\Http\Controllers\Admin\CloudinaryMediaController::class, 'download'])->name('media.download');
        Route::delete('/media/delete', [\App\Http\Controllers\Admin\CloudinaryMediaController::class, 'destroy'])->name('media.destroy');
        
        // Vendor Verification Management
        Route::get('/verifications', [VendorVerificationController::class, 'index'])->name('verifications.index');
        Route::post('/verifications/{id}/approve', [VendorVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{id}/reject', [VendorVerificationController::class, 'reject'])->name('verifications.reject');
        Route::post('/verifications/{vendorId}/suspend', [VendorVerificationController::class, 'suspend'])->name('verifications.suspend');
        Route::post('/verifications/{vendorId}/cancel', [VendorVerificationController::class, 'cancelVerification'])->name('verifications.cancel');
        
        // Vendor Management (Admin)
        Route::get('/vendor-management', [App\Http\Controllers\Admin\VendorController::class, 'index'])->name('vendors.index');
        Route::get('/vendor-management/{id}', [App\Http\Controllers\Admin\VendorController::class, 'show'])->name('vendors.show');
        Route::post('/vendor-management/{id}/verify', [App\Http\Controllers\Admin\VendorController::class, 'verify'])->name('vendors.verify');
        Route::post('/vendor-management/{id}/unverify', [App\Http\Controllers\Admin\VendorController::class, 'unverify'])->name('vendors.unverify');
        Route::post('/vendor-management/{id}/deactivate', [App\Http\Controllers\Admin\VendorController::class, 'deactivate'])->name('vendors.deactivate');
        Route::post('/vendor-management/{id}/activate', [App\Http\Controllers\Admin\VendorController::class, 'activate'])->name('vendors.activate');
        Route::post('/vendor-management/{id}/reset-password', [App\Http\Controllers\Admin\VendorController::class, 'resetPassword'])->name('vendors.resetPassword');
        Route::delete('/vendor-management/{id}', [App\Http\Controllers\Admin\VendorController::class, 'destroy'])->name('vendors.destroy');
        
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
        
        // Featured Ads Management (Admin) - Use /manage-featured-ads to avoid vendor route conflict
        Route::get('/manage-featured-ads', [AdminFeaturedAdController::class, 'index'])->name('featured-ads.index');
        Route::get('/manage-featured-ads/create', [AdminFeaturedAdController::class, 'create'])->name('featured-ads.create');
        Route::post('/manage-featured-ads', [AdminFeaturedAdController::class, 'store'])->name('featured-ads.store');
        Route::get('/manage-featured-ads/export/csv', [AdminFeaturedAdController::class, 'export'])->name('featured-ads.export');
        Route::get('/manage-featured-ads/{id}', [AdminFeaturedAdController::class, 'show'])->name('featured-ads.show');
        Route::get('/manage-featured-ads/{id}/edit', [AdminFeaturedAdController::class, 'edit'])->name('featured-ads.edit');
        Route::put('/manage-featured-ads/{id}', [AdminFeaturedAdController::class, 'update'])->name('featured-ads.update');
        Route::delete('/manage-featured-ads/{id}', [AdminFeaturedAdController::class, 'destroy'])->name('featured-ads.destroy');
        Route::post('/manage-featured-ads/{id}/approve', [AdminFeaturedAdController::class, 'approve'])->name('featured-ads.approve');
        Route::post('/manage-featured-ads/{id}/reject', [AdminFeaturedAdController::class, 'reject'])->name('featured-ads.reject');
        Route::post('/manage-featured-ads/{id}/suspend', [AdminFeaturedAdController::class, 'suspend'])->name('featured-ads.suspend');
        Route::get('/vendors/{vendorId}/services', [AdminFeaturedAdController::class, 'getVendorServices'])->name('vendors.services');
        
        // VIP Plans Management (Admin Only) - Use /manage-vip-plans to avoid vendor route conflict
        Route::get('/manage-vip-plans', [VipPlanController::class, 'index'])->name('vip-plans.index');
        Route::get('/manage-vip-plans/create', [VipPlanController::class, 'create'])->name('vip-plans.create');
        Route::post('/manage-vip-plans', [VipPlanController::class, 'store'])->name('vip-plans.store');
        Route::get('/manage-vip-plans/{id}', [VipPlanController::class, 'show'])->name('vip-plans.show');
        Route::get('/manage-vip-plans/{id}/edit', [VipPlanController::class, 'edit'])->name('vip-plans.edit');
        Route::put('/manage-vip-plans/{id}', [VipPlanController::class, 'update'])->name('vip-plans.update');
        Route::post('/manage-vip-plans/{id}/toggle-status', [VipPlanController::class, 'toggleStatus'])->name('vip-plans.toggle-status');
        Route::delete('/manage-vip-plans/{id}', [VipPlanController::class, 'destroy'])->name('vip-plans.destroy');
        
        // VIP Subscriptions Management (Admin Only) - Use /manage-vip-subscriptions to avoid vendor route conflict
        Route::get('/manage-vip-subscriptions', [AdminVipSubscriptionController::class, 'index'])->name('vip-subscriptions.index');
        Route::get('/manage-vip-subscriptions/create', [AdminVipSubscriptionController::class, 'create'])->name('vip-subscriptions.create');
        Route::post('/manage-vip-subscriptions', [AdminVipSubscriptionController::class, 'store'])->name('vip-subscriptions.store');
        Route::get('/manage-vip-subscriptions/export/csv', [AdminVipSubscriptionController::class, 'export'])->name('vip-subscriptions.export');
        Route::get('/manage-vip-subscriptions/{id}', [AdminVipSubscriptionController::class, 'show'])->name('vip-subscriptions.show');
        Route::get('/manage-vip-subscriptions/{id}/edit', [AdminVipSubscriptionController::class, 'edit'])->name('vip-subscriptions.edit');
        Route::put('/manage-vip-subscriptions/{id}', [AdminVipSubscriptionController::class, 'update'])->name('vip-subscriptions.update');
        Route::post('/manage-vip-subscriptions/{id}/cancel', [AdminVipSubscriptionController::class, 'cancel'])->name('vip-subscriptions.cancel');
        Route::post('/manage-vip-subscriptions/{id}/reactivate', [AdminVipSubscriptionController::class, 'reactivate'])->name('vip-subscriptions.reactivate');
        Route::post('/manage-vip-subscriptions/{id}/extend', [AdminVipSubscriptionController::class, 'extend'])->name('vip-subscriptions.extend');
        Route::delete('/manage-vip-subscriptions/{id}', [AdminVipSubscriptionController::class, 'destroy'])->name('vip-subscriptions.destroy');
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
        
        // Sample Work Management
        Route::get('/sample-work', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'index'])->name('sample-work');
        Route::post('/sample-work/images', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'uploadImages'])->name('sample-work.images.upload');
        Route::delete('/sample-work/images', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'deleteImage'])->name('sample-work.images.delete');
        Route::post('/sample-work/preview', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'setPreview'])->name('sample-work.preview');
        Route::post('/sample-work/video', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'uploadVideo'])->name('sample-work.video.upload');
        Route::delete('/sample-work/video', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'deleteVideo'])->name('sample-work.video.delete');
        Route::post('/sample-work/title', [\App\Http\Controllers\Vendor\SampleWorkController::class, 'updateTitle'])->name('sample-work.title');
        
        // Subscription Plans
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
        Route::post('/subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
        
        // Vendor Tour
        Route::post('/tour/complete', [VendorDashboardControllerNew::class, 'completeTour'])->name('tour.complete');
        Route::post('/tour/restart', [VendorDashboardControllerNew::class, 'restartTour'])->name('tour.restart');
        
        // Chat System Routes
        Route::get('/messages', [\App\Http\Controllers\Vendor\MessageController::class, 'index'])->name('messages');
        Route::get('/messages/client/{clientId}', [\App\Http\Controllers\Vendor\MessageController::class, 'getConversation'])->name('messages.conversation');
        Route::post('/messages/client/{clientId}', [\App\Http\Controllers\Vendor\MessageController::class, 'sendMessage'])->name('messages.send');
        Route::delete('/messages/{messageId}', [\App\Http\Controllers\Vendor\MessageController::class, 'deleteMessage'])->name('messages.delete');
        Route::post('/messages/typing/{clientId}', [\App\Http\Controllers\Vendor\MessageController::class, 'typing'])->name('messages.typing');
        Route::post('/messages/stop-typing/{clientId}', [\App\Http\Controllers\Vendor\MessageController::class, 'stopTyping'])->name('messages.stop-typing');
        Route::post('/status/online', [\App\Http\Controllers\Vendor\MessageController::class, 'updateOnlineStatus'])->name('status.update');
        
        // Featured Ads
        Route::get('/featured-ads', [FeaturedAdController::class, 'index'])->name('featured-ads.index');
        Route::get('/featured-ads/create', [FeaturedAdController::class, 'create'])->name('featured-ads.create');
        Route::post('/featured-ads', [FeaturedAdController::class, 'store'])->name('featured-ads.store');
        Route::get('/featured-ads/{id}', [FeaturedAdController::class, 'show'])->name('featured-ads.show');
        Route::get('/featured-ads/{id}/edit', [FeaturedAdController::class, 'edit'])->name('featured-ads.edit');
        Route::put('/featured-ads/{id}', [FeaturedAdController::class, 'update'])->name('featured-ads.update');
        Route::delete('/featured-ads/{id}', [FeaturedAdController::class, 'destroy'])->name('featured-ads.destroy');
        Route::get('/featured-ads/{id}/payment', [FeaturedAdController::class, 'payment'])->name('featured-ads.payment');
        Route::post('/featured-ads/{id}/verify-payment', [FeaturedAdController::class, 'verifyPayment'])->name('featured-ads.verify-payment');
        
        // VIP Subscriptions
        Route::get('/vip-subscriptions', [VipSubscriptionController::class, 'index'])->name('vip-subscriptions.index');
        Route::get('/vip-subscriptions/{planId}/subscribe', [VipSubscriptionController::class, 'subscribe'])->name('vip-subscriptions.subscribe');
        Route::post('/vip-subscriptions/{planId}/process', [VipSubscriptionController::class, 'processSubscription'])->name('vip-subscriptions.process');
        Route::get('/vip-subscriptions/{id}/payment', [VipSubscriptionController::class, 'payment'])->name('vip-subscriptions.payment');
        Route::post('/vip-subscriptions/{id}/verify-payment', [VipSubscriptionController::class, 'verifyPayment'])->name('vip-subscriptions.verify-payment');
        Route::post('/vip-subscriptions/{id}/cancel', [VipSubscriptionController::class, 'cancel'])->name('vip-subscriptions.cancel');
    });

    // Client Routes
    // Note: Client dashboard is served via main /dashboard route
    Route::middleware(['role:client'])->name('client.')->group(function () {
        // Favorites/Bookmarks
        Route::get('/favorites', [\App\Http\Controllers\Client\FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/{vendorId}', [\App\Http\Controllers\Client\FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorites/{vendorId}', [\App\Http\Controllers\Client\FavoriteController::class, 'destroy'])->name('favorites.destroy');

        // Chat System Routes (Messages)
        Route::get('/conversations', [\App\Http\Controllers\Client\MessageController::class, 'index'])->name('conversations');
        Route::get('/messages/vendor/{vendorId}', [\App\Http\Controllers\Client\MessageController::class, 'getConversation'])->name('messages.conversation');
        Route::post('/messages/vendor/{vendorId}', [\App\Http\Controllers\Client\MessageController::class, 'sendMessage'])->name('messages.send');
        Route::post('/messages/typing/{vendorId}', [\App\Http\Controllers\Client\MessageController::class, 'typing'])->name('messages.typing');
        Route::post('/messages/stop-typing/{vendorId}', [\App\Http\Controllers\Client\MessageController::class, 'stopTyping'])->name('messages.stop-typing');

        // Support
        Route::get('/support', [\App\Http\Controllers\Client\SupportController::class, 'index'])->name('support.index');
        Route::post('/support', [\App\Http\Controllers\Client\SupportController::class, 'store'])->name('support.store');

        // Settings (using /preferences to avoid conflict with super admin /settings)
        Route::get('/preferences', [\App\Http\Controllers\Client\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/preferences/notifications', [\App\Http\Controllers\Client\SettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::post('/preferences/privacy', [\App\Http\Controllers\Client\SettingsController::class, 'updatePrivacy'])->name('settings.privacy');
        Route::post('/preferences/password', [\App\Http\Controllers\Client\SettingsController::class, 'updatePassword'])->name('settings.password');
    });

    // ============================================================
    // Notification Routes (All Authenticated Users)
    // ============================================================
    Route::get('notifications/unread', [App\Http\Controllers\NotificationController::class, 'getUnreadNotifications']);
    Route::post('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::post('notifications/messages/read-all', [App\Http\Controllers\NotificationController::class, 'markMessageNotificationsAsRead']);
    Route::get('notifications/{id}/redirect', [App\Http\Controllers\NotificationController::class, 'getRedirectUrl']);

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
    
    // Generic message store route (for vendor-sidebar and similar components)
    Route::post('/messages', [MessageController::class, 'sendMessage'])->name('messages.store');
    
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
