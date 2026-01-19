<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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
        $lokasi = Lokasi::orderBy('nama_lokasi')->get();
        
        return view('sarpras.create', compact('kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kategori_id' => 'required|exists:kategori,id',
            'lokasi_id' => 'required|exists:lokasi,id',
            'stok' => 'required|integer|min:0',
            'stok_rusak' => 'nullable|integer|min:0',
            'kondisi_awal' => 'required|in:baik,rusak',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'lokasi_id.required' => 'Lokasi wajib dipilih.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok_rusak.min' => 'Stok rusak tidak boleh negatif.',
        ]);

        // Handle foto upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('sarpras', 'public');
        }

        Sarpras::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'foto' => $fotoPath,
            'kategori_id' => $validated['kategori_id'],
            'lokasi_id' => $validated['lokasi_id'],
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
        $lokasi = Lokasi::orderBy('nama_lokasi')->get();
        
        return view('sarpras.edit', compact('sarpras', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras,kode_barang,' . $sarpras->id,
            'nama_barang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kategori_id' => 'required|exists:kategori,id',
            'lokasi_id' => 'required|exists:lokasi,id',
            'stok' => 'required|integer|min:0',
            'stok_rusak' => 'nullable|integer|min:0',
            'kondisi_awal' => 'required|in:baik,rusak',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'lokasi_id.required' => 'Lokasi wajib dipilih.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok_rusak.min' => 'Stok rusak tidak boleh negatif.',
        ]);

        // Handle foto upload
        $fotoPath = $sarpras->foto; // Keep existing photo
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($sarpras->foto && Storage::disk('public')->exists($sarpras->foto)) {
                Storage::disk('public')->delete($sarpras->foto);
            }
            $fotoPath = $request->file('foto')->store('sarpras', 'public');
        }

        // Handle foto removal (checkbox checked)
        if ($request->has('hapus_foto') && $request->hapus_foto) {
            if ($sarpras->foto && Storage::disk('public')->exists($sarpras->foto)) {
                Storage::disk('public')->delete($sarpras->foto);
            }
            $fotoPath = null;
        }

        $sarpras->update([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'foto' => $fotoPath,
            'kategori_id' => $validated['kategori_id'],
            'lokasi_id' => $validated['lokasi_id'],
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
