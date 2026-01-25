<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Pengaduan::with(['user', 'sarpras', 'lokasi', 'petugas'])
            ->latest();

        // Filter role
        if ($user->role === 'peminjam') {
            $query->where('user_id', $user->id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pengaduan = $query->paginate(10)->withQueryString();

        return view('pengaduan.index', compact('pengaduan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasi = \App\Models\Lokasi::orderBy('nama_lokasi')->get();
        // Hanya sarpras parent yang ditampilkan
        $sarpras = \App\Models\Sarpras::orderBy('nama_barang')->get();

        return view('pengaduan.create', compact('lokasi', 'sarpras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi_id' => 'required|exists:lokasi,id',
            'sarpras_id' => 'nullable|exists:sarpras,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
        }

        \App\Models\Pengaduan::create([
            'user_id' => auth()->id(),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi_id' => $validated['lokasi_id'],
            'sarpras_id' => $validated['sarpras_id'] ?? null,
            'foto' => $fotoPath,
            'status' => 'belum_ditindaklanjuti',
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Pengaduan berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Pengaduan $pengaduan)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->role === 'peminjam' && $pengaduan->user_id !== $user->id) {
            abort(403);
        }

        return view('pengaduan.show', compact('pengaduan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Pengaduan $pengaduan)
    {
        // Admin/Petugas only blocked by route middleware usually, but good to check
        if (auth()->user()->role === 'peminjam') {
            abort(403);
        }

        return view('pengaduan.edit', compact('pengaduan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Pengaduan $pengaduan)
    {
        if (auth()->user()->role === 'peminjam') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:belum_ditindaklanjuti,sedang_diproses,selesai,ditutup',
            'catatan_petugas' => 'nullable|string',
        ]);

        $pengaduan->update([
            'status' => $validated['status'],
            'catatan_petugas' => $validated['catatan_petugas'],
            'petugas_id' => auth()->id(),
        ]);

        // Send notification to user
        $statusLabels = [
            'sedang_diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'ditutup' => 'Ditutup',
        ];

        if (isset($statusLabels[$validated['status']])) {
            \App\Models\Notification::send(
                $pengaduan->user_id,
                \App\Models\Notification::TYPE_PENGADUAN_UPDATED,
                'Pengaduan Diperbarui 📢',
                'Pengaduan "'.$pengaduan->judul.'" telah diubah ke status: '.$statusLabels[$validated['status']],
                route('pengaduan.show', $pengaduan)
            );
        }

        return redirect()->route('pengaduan.index')->with('success', 'Status pengaduan diperbarui.');
    }
}
