<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InteractionController;
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

// Search & Filter Vendors
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Public Vendor Registration (for non-logged-in users)
Route::get('/signup/vendor', [PublicVendorController::class, 'create'])->name('vendor.public.register');
Route::post('/signup/vendor', [PublicVendorController::class, 'store'])->name('vendor.public.store');

// Public Vendor Profiles (no auth required)
Route::get('/vendors', [VendorProfileController::class, 'index'])->name('vendors.index');
Route::get('/vendors/{slug}', [VendorProfileController::class, 'show'])->name('vendors.show');

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

// Role-Based Dashboards
// Super Admin Dashboard
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
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

// Admin Dashboard
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Vendor Verification Management
    Route::get('verifications', [VendorVerificationController::class, 'index'])->name('verifications.index');
    Route::post('verifications/{id}/approve', [VendorVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('verifications/{id}/reject', [VendorVerificationController::class, 'reject'])->name('verifications.reject');
    
    // Client Management
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::post('clients/{id}/deactivate', [ClientController::class, 'deactivate'])->name('clients.deactivate');
    Route::post('clients/{id}/activate', [ClientController::class, 'activate'])->name('clients.activate');
    Route::post('clients/{id}/reset-password', [ClientController::class, 'resetPassword'])->name('clients.resetPassword');
    
    // Reports & Issues Management
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/{id}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('reports/{id}/reopen', [ReportController::class, 'reopen'])->name('reports.reopen');
});

// Vendor Dashboard  
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardControllerNew::class, 'index'])->name('dashboard');
    
    // Service Management (CRUD)
    Route::resource('services', ServiceController::class);
    
    // Verification Request
    Route::get('verification', [VerificationController::class, 'index'])->name('verification');
    Route::post('verification', [VerificationController::class, 'store'])->name('verification.store');
    
    // Subscription Plans
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
});

// Client Dashboard
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
});

// Default Dashboard (redirects based on role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('super_admin')) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('vendor')) {
        return redirect()->route('vendor.dashboard');
    } elseif ($user->hasRole('client')) {
        return redirect()->route('client.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Vendor Registration Routes (for existing users to upgrade)
    Route::get('/vendor/register', [VendorRegistrationController::class, 'create'])->name('vendor.register');
    Route::post('/vendor/register', [VendorRegistrationController::class, 'store'])->name('vendor.store');
    
    // Review Submission (authenticated users only)
    Route::post('/vendors/{vendor}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // User Interaction Logging (with rate limiting)
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/interactions/vendors/{vendor}/view', [InteractionController::class, 'logView'])->name('interactions.view');
        Route::post('/interactions/search', [InteractionController::class, 'logSearch'])->name('interactions.search');
        Route::post('/interactions/category', [InteractionController::class, 'logCategoryView'])->name('interactions.category');
        Route::post('/interactions/vendors/{vendor}/recommendation-click', [InteractionController::class, 'logRecommendationClick'])->name('interactions.recommendation');
    });
});

require __DIR__.'/auth.php';
