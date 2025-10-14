<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vendor;

$slug = 'test-vendor-business-1760402246';

$vendor = Vendor::where('slug', $slug)->first();

if (!$vendor) {
    echo "❌ Vendor not found!\n";
    exit(1);
}

$vendor->is_verified = true;
$vendor->verified_at = now();
$vendor->verification_status = 'approved';
$vendor->save();

echo "✅ Vendor verified successfully!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Business Name: {$vendor->business_name}\n";
echo "Slug: {$vendor->slug}\n";
echo "Verified: Yes ✓\n";
echo "Profile URL: http://localhost:8000/vendors/{$vendor->slug}\n";

