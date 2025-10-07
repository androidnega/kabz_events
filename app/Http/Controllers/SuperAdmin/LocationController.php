<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Region;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Display Ghana locations
     */
    public function index()
    {
        $regions = Region::with('districts.towns')->orderBy('name')->get();
        return view('superadmin.settings.locations', compact('regions'));
    }

    /**
     * Store a new location
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:region,district,town',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
        ]);

        if ($request->type === 'region') {
            Region::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
        } elseif ($request->type === 'district') {
            District::create([
                'region_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
        } else {
            Town::create([
                'district_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
        }

        return back()->with('success', 'Location added successfully! 🇬🇭');
    }

    /**
     * Delete a location
     */
    public function destroy($id, Request $request)
    {
        $request->validate(['type' => 'required|in:region,district,town']);

        if ($request->type === 'region') {
            Region::find($id)?->delete();
        } elseif ($request->type === 'district') {
            District::find($id)?->delete();
        } else {
            Town::find($id)?->delete();
        }

        return back()->with('success', 'Location deleted successfully.');
    }

    /**
     * Show CSV upload form
     */
    public function uploadForm()
    {
        $stats = [
            'total_regions' => Region::count(),
            'total_districts' => District::count(),
            'total_towns' => Town::count(),
        ];

        return view('superadmin.settings.locations_upload', compact('stats'));
    }

    /**
     * Import locations from CSV file
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();
            $rows = array_map('str_getcsv', file($path));
            $header = array_map('strtolower', array_map('trim', $rows[0]));

            $imported = 0;
            $errors = [];

            for ($i = 1; $i < count($rows); $i++) {
                // Skip empty rows
                if (empty(array_filter($rows[$i]))) {
                    continue;
                }

                $row = array_combine($header, array_map('trim', $rows[$i]));

                // Validate required columns
                if (!isset($row['region']) || !isset($row['district']) || !isset($row['town'])) {
                    $errors[] = "Row {$i}: Missing required columns";
                    continue;
                }

                $regionName = $row['region'];
                $districtName = $row['district'];
                $townName = $row['town'];

                // Create or get region
                $region = Region::firstOrCreate(
                    ['name' => $regionName],
                    ['slug' => Str::slug($regionName)]
                );

                // Create or get district
                $district = District::firstOrCreate(
                    ['region_id' => $region->id, 'name' => $districtName],
                    ['slug' => Str::slug($districtName)]
                );

                // Create or get town
                Town::firstOrCreate(
                    ['district_id' => $district->id, 'name' => $townName],
                    ['slug' => Str::slug($townName)]
                );

                $imported++;
            }

            $message = "{$imported} locations imported successfully! 🇬🇭";
            if (count($errors) > 0) {
                $message .= " (" . count($errors) . " rows skipped due to errors)";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
