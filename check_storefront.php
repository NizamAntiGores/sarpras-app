<?php

use App\Models\Lokasi;
use App\Models\SarprasUnit;
use App\Models\Sarpras;

echo "--- CHECKING STOREFRONT LOCATIONS ---\n";
$storefronts = Lokasi::where('is_storefront', true)->get();

if ($storefronts->isEmpty()) {
    echo "CRITICAL: No location found with is_storefront = true.\n";
} else {
    foreach ($storefronts as $loc) {
        echo "Found Storefront: {$loc->nama_lokasi} (ID: {$loc->id})\n";
    }
}

echo "\n--- CHECKING UNITS IN STOREFRONT ---\n";
foreach ($storefronts as $loc) {
    $units = SarprasUnit::where('lokasi_id', $loc->id)->count();
    echo "Location '{$loc->nama_lokasi}' has {$units} units.\n";

    // Sample units
    $samples = SarprasUnit::where('lokasi_id', $loc->id)->take(3)->get();
    foreach ($samples as $sample) {
        echo " - [{$sample->kode_unit}] {$sample->sarpras->nama_barang} (Status: {$sample->status})\n";
    }
}

echo "\n--- CHECKING CONTROLLER QUERY LOGIC ---\n";
// Simulate the query used in Controller
$availableUnits = SarprasUnit::with('sarpras', 'lokasi')
    ->bisaDipinjam()
    ->whereHas('lokasi', function ($q) {
        $q->where('is_storefront', true);
    })
    ->take(5)
    ->get();

echo "Query returned " . $availableUnits->count() . " units.\n";
foreach ($availableUnits as $unit) {
    echo " -> [Available] {$unit->kode_unit} at {$unit->lokasi->nama_lokasi}\n";
}
