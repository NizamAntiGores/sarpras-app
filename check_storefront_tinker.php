<?php
// Run this via: php artisan tinker check_storefront.php

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
    if (!$loc)
        continue;
    $units = SarprasUnit::where('lokasi_id', $loc->id)->count();
    $stocks = \App\Models\ItemStock::where('lokasi_id', $loc->id)->count();

    echo "Location '{$loc->nama_lokasi}' has:\n";
    echo "  - {$units} Units (Assets)\n";
    echo "  - {$stocks} Stock Records (Consumables)\n";

    // Sample units
    $samples = SarprasUnit::where('lokasi_id', $loc->id)->with('sarpras')->take(3)->get();
    foreach ($samples as $sample) {
        $sarprasName = $sample->sarpras ? $sample->sarpras->nama_barang : 'Unknown';
        echo "  -> Unit: [{$sample->kode_unit}] {$sarprasName} (Status: {$sample->status})\n";
    }

    // Sample stocks
    $stockSamples = \App\Models\ItemStock::where('lokasi_id', $loc->id)->with('sarpras')->take(3)->get();
    foreach ($stockSamples as $s) {
        $sarprasName = $s->sarpras ? $s->sarpras->nama_barang : 'Unknown';
        echo "  -> Stock: {$sarprasName} = {$s->quantity}\n";
    }
}

echo "\n--- CHECKING CONTROLLER QUERY LOGIC ---\n";
// Simulate the query used in Controller
try {
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
} catch (\Exception $e) {
    echo "Query Error: " . $e->getMessage() . "\n";
}
