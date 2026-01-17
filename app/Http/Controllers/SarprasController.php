<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SarprasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sarpras = Sarpras::with(['kategori', 'peminjaman' => function ($query) {
                $query->where('status', 'disetujui')->with('user');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sarpras.index', compact('sarpras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();
        
        return view('sarpras.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'lokasi' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'stok_rusak' => 'nullable|integer|min:0',
            'kondisi_awal' => 'required|in:baik,rusak',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok_rusak.min' => 'Stok rusak tidak boleh negatif.',
        ]);

        Sarpras::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori_id' => $validated['kategori_id'],
            'lokasi' => $validated['lokasi'],
            'stok' => $validated['stok'],
            'stok_rusak' => $validated['stok_rusak'] ?? 0,
            'kondisi_awal' => $validated['kondisi_awal'],
        ]);

        return redirect()
            ->route('sarpras.index')
            ->with('success', 'Data sarpras berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sarpras $sarpras): View
    {
        $sarpras->load('kategori', 'peminjaman.user');
        
        return view('sarpras.show', compact('sarpras'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sarpras $sarpras): View
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();
        
        return view('sarpras.edit', compact('sarpras', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras,kode_barang,' . $sarpras->id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'lokasi' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'stok_rusak' => 'nullable|integer|min:0',
            'kondisi_awal' => 'required|in:baik,rusak',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok_rusak.min' => 'Stok rusak tidak boleh negatif.',
        ]);

        $sarpras->update([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori_id' => $validated['kategori_id'],
            'lokasi' => $validated['lokasi'],
            'stok' => $validated['stok'],
            'stok_rusak' => $validated['stok_rusak'] ?? 0,
            'kondisi_awal' => $validated['kondisi_awal'],
        ]);

        return redirect()
            ->route('sarpras.index')
            ->with('success', 'Data sarpras berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * HANYA ADMIN yang bisa menghapus sarpras
     */
    public function destroy(Sarpras $sarpras): RedirectResponse
    {
        // Proteksi backend: Hanya admin yang bisa hapus
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus data sarpras.');
        }

        // Cek apakah ada peminjaman aktif
        $activePeminjaman = $sarpras->peminjaman()
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($activePeminjaman) {
            return redirect()
                ->route('sarpras.index')
                ->with('error', 'Tidak dapat menghapus sarpras yang sedang dipinjam atau memiliki peminjaman aktif.');
        }

        $sarpras->delete();

        return redirect()
            ->route('sarpras.index')
            ->with('success', 'Data sarpras berhasil dihapus.');
    }
}
