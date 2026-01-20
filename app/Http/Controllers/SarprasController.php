<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\Kategori;
use App\Models\SarprasUnit;
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
        $sarpras = Sarpras::with(['kategori'])
            ->withCount([
                'units as total_unit' => function ($query) {
                    $query->aktif();
                },
                'units as tersedia_count' => function ($query) {
                    $query->bisaDipinjam();
                },
                'units as dipinjam_count' => function ($query) {
                    $query->where('status', SarprasUnit::STATUS_DIPINJAM);
                },
                'units as maintenance_count' => function ($query) {
                    $query->where('status', SarprasUnit::STATUS_MAINTENANCE);
                },
            ])
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
     * Catatan: Stok tidak lagi diinput di sini, tapi ditambahkan melalui unit management
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kategori_id' => 'required|exists:kategori,id',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
        ]);

        // Handle foto upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('sarpras', 'public');
        }

        $sarpras = Sarpras::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'foto' => $fotoPath,
            'kategori_id' => $validated['kategori_id'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()
            ->route('sarpras.units.create', $sarpras)
            ->with('success', 'Master barang berhasil ditambahkan. Silakan tambahkan unit barang.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sarpras $sarpras): View
    {
        $sarpras->load([
            'kategori',
            'units' => function ($query) {
                $query->aktif()->with('lokasi')->orderBy('kode_unit');
            }
        ]);
        
        // Summary statistics
        $statistics = [
            'total_unit' => $sarpras->units->count(),
            'tersedia' => $sarpras->units->where('status', SarprasUnit::STATUS_TERSEDIA)
                ->where('kondisi', '!=', SarprasUnit::KONDISI_RUSAK_BERAT)->count(),
            'dipinjam' => $sarpras->units->where('status', SarprasUnit::STATUS_DIPINJAM)->count(),
            'maintenance' => $sarpras->units->where('status', SarprasUnit::STATUS_MAINTENANCE)->count(),
            'rusak' => $sarpras->units->where('kondisi', '!=', SarprasUnit::KONDISI_BAIK)->count(),
        ];
        
        return view('sarpras.show', compact('sarpras', 'statistics'));
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kategori_id' => 'required|exists:kategori,id',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
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
            'deskripsi' => $validated['deskripsi'] ?? null,
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

        // Cek apakah ada unit yang sedang dipinjam
        $activeUnits = $sarpras->units()
            ->where('status', SarprasUnit::STATUS_DIPINJAM)
            ->exists();

        if ($activeUnits) {
            return redirect()
                ->route('sarpras.index')
                ->with('error', 'Tidak dapat menghapus sarpras yang memiliki unit sedang dipinjam.');
        }

        // Soft delete: hapusbukukan semua unit
        $sarpras->units()->update(['status' => SarprasUnit::STATUS_DIHAPUSBUKUKAN]);
        
        // Delete master sarpras (atau bisa juga soft delete jika mau)
        $namaBarang = $sarpras->nama_barang;
        $sarpras->delete();

        \App\Helpers\LogHelper::record('delete', "Menghapus sarpras: {$namaBarang}");

        return redirect()
            ->route('sarpras.index')
            ->with('success', 'Data sarpras berhasil dihapus.');
    }
}
