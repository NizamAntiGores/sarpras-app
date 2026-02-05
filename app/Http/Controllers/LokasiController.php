<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $lokasi = Lokasi::withCount([
            'units as units_count' => function ($query) {
                $query->where('status', '!=', \App\Models\SarprasUnit::STATUS_DIHAPUSBUKUKAN);
            }
        ])
            ->orderBy('nama_lokasi')
            ->paginate(25);

        return view('lokasi.index', compact('lokasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('lokasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasi,nama_lokasi',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique' => 'Nama lokasi sudah ada.',
        ]);

        Lokasi::create($validated);

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lokasi $lokasi): View
    {
        return view('lokasi.edit', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lokasi $lokasi): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasi,nama_lokasi,' . $lokasi->id,
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique' => 'Nama lokasi sudah ada.',
        ]);

        $lokasi->update($validated);

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        // Check if lokasi is being used by sarpras
        if ($lokasi->sarpras()->exists()) {
            return redirect()
                ->route('lokasi.index')
                ->with('error', 'Tidak dapat menghapus lokasi yang masih digunakan oleh sarpras.');
        }

        $lokasi->delete();

        \App\Helpers\LogHelper::record('delete', "Menghapus lokasi: {$lokasi->nama_lokasi}");

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
