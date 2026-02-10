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

        // Filter role - Siswa/Guru/Peminjam hanya bisa lihat pengaduan sendiri
        if ($user->isPeminjam()) {
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
            'jenis' => 'required|in:tempat,barang',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:20',
            'lokasi_id' => 'nullable|exists:lokasi,id',
            'lokasi_lainnya' => 'nullable|string|max:255',
            'sarpras_id' => 'nullable|exists:sarpras,id',
            'barang_lainnya' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ], [
            'jenis.required' => 'Pilih jenis pengaduan.',
        ]);

        // Custom validation: harus pilih salah satu (dropdown atau lainnya)
        if ($validated['jenis'] === 'tempat') {
            if (empty($validated['lokasi_id']) && empty($validated['lokasi_lainnya'])) {
                return back()->withInput()->withErrors(['lokasi_id' => 'Pilih lokasi atau isi "Lainnya".']);
            }
        } elseif ($validated['jenis'] === 'barang') {
            if (empty($validated['sarpras_id']) && empty($validated['barang_lainnya'])) {
                return back()->withInput()->withErrors(['sarpras_id' => 'Pilih barang atau isi "Lainnya".']);
            }
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
        }

        \App\Models\Pengaduan::create([
            'user_id' => auth()->id(),
            'jenis' => $validated['jenis'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi_id' => $validated['lokasi_id'] ?? null,
            'lokasi_lainnya' => $validated['lokasi_lainnya'] ?? null,
            'sarpras_id' => $validated['sarpras_id'] ?? null,
            'barang_lainnya' => $validated['barang_lainnya'] ?? null,
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

        $pengaduan->load(['user', 'sarpras', 'lokasi', 'responses.user']);

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
     * Update the specified resource in storage (Add Response).
     */
    public function update(Request $request, \App\Models\Pengaduan $pengaduan)
    {
        $user = auth()->user();

        // Authorization: Admin/Petugas OR The Owner of the complaint
        if ($user->role === 'peminjam' || $user->role === 'siswa' || $user->role === 'guru') {
             if ($pengaduan->user_id !== $user->id) {
                 abort(403);
             }
        }

        // Check if locked
        if (in_array($pengaduan->status, ['selesai', 'ditutup', 'ditolak'])) {
            return back()->with('error', 'Laporan sudah ditutup. Tidak dapat menambahkan respon.');
        }

        $validated = $request->validate([
            'status' => 'required|in:belum_ditindaklanjuti,sedang_diproses,selesai,ditutup,chat',
            'response' => 'required|string|min:5',
        ]);

        $inputStatus = $validated['status'];
        $responseMessage = $validated['response'];

        // Determine if this is a status change or just a chat
        $isStatusChange = ($inputStatus !== 'chat' && $inputStatus !== $pengaduan->status);
        
        // If user is not admin/petugas, force 'chat' mode (cannot change status)
        if (!in_array($user->role, ['admin', 'petugas'])) {
            $inputStatus = 'chat';
            $isStatusChange = false;
        }

        // Record Response
        // We set 'status' in response ONLY if there is a meaningful status change/action
        // If it's just 'chat', we store NULL validly
        \App\Models\PengaduanResponse::create([
            'pengaduan_id' => $pengaduan->id,
            'user_id' => $user->id,
            'status' => $isStatusChange ? $inputStatus : null, // Null means "Just Chat/No Status Change"
            'response' => $responseMessage,
        ]);

        // Update Parent Status and Petugas Note Only if Changed/Admin
        if ($isStatusChange) {
            $pengaduan->update([
                'status' => $inputStatus,
                'petugas_id' => $user->id,
                'catatan_petugas' => $responseMessage, 
            ]);

            // Notification for Status Change
            $statusLabels = [
                'sedang_diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'ditutup' => 'Ditutup',
                'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti'
            ];

            \App\Models\Notification::send(
                $pengaduan->user_id,
                \App\Models\Notification::TYPE_PENGADUAN_UPDATED,
                'Status Pengaduan Diperbarui',
                "Status pengaduan Anda berubah menjadi: " . ($statusLabels[$inputStatus] ?? $inputStatus),
                route('pengaduan.show', $pengaduan)
            );
        } else {
             // Notification for Chat Reply
             // If Admin replies -> Notify User
             // If User replies -> Notify Admin (optional, or just silent)
             if (in_array($user->role, ['admin', 'petugas'])) {
                 $pengaduan->update(['petugas_id' => $user->id]); // Claim ownership if replying?
                 
                 \App\Models\Notification::send(
                    $pengaduan->user_id,
                    \App\Models\Notification::TYPE_PENGADUAN_UPDATED,
                    'Tanggapan Baru dari Admin',
                    "Admin menanggapi pengaduan Anda: \"{$responseMessage}\"",
                    route('pengaduan.show', $pengaduan)
                );
             }
        }
        
        // Activity Log
        // ... (Keep existing log logic or simplify)

        return redirect()->route('pengaduan.show', $pengaduan)->with('success', 'Respon berhasil dikirim.');
    }
}
