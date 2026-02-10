<?php

namespace App\Http\Controllers;

use App\Models\BarangHilang;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\SarprasUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /**
     * Lookup peminjaman by QR code and redirect based on status.
     * 
     * Logic:
     * - Jika status 'disetujui' DAN belum handover -> redirect ke handover (serah terima)
     * - Jika status 'disetujui' DAN sudah handover -> redirect ke pengembalian
     * - Selain itu -> tampilkan pesan error sesuai kondisi
     */
    public function lookupByQrCode(Request $request)
    {
        $qrCode = $request->input('qr_code');

        if (! $qrCode) {
            return redirect()->route('peminjaman.index')->with('error', 'Kode QR tidak boleh kosong.');
        }

        $peminjaman = Peminjaman::where('qr_code', $qrCode)->first();

        if (! $peminjaman) {
            return redirect()->route('peminjaman.index')->with('error', 'Peminjaman dengan kode QR tersebut tidak ditemukan.');
        }

        // Route based on current state
        if (in_array($peminjaman->status, ['menunggu', 'ditolak', 'selesai'])) {
             // Fallback redirects handled by lookup or just generic error
             return redirect()->route('peminjaman.show', $peminjaman)->with('error', 'Status peminjaman tidak valid for pengembalian.');
        }

        // Route based on current state
        // If status is disetujui and not handed over yet, go to handover
        if ($peminjaman->status === 'disetujui' && $peminjaman->isReadyForPickup()) {
             return redirect()->route('peminjaman.handover', $peminjaman);
        }
        
        // If status is dipinjam OR (disetujui but handled over logic mismatch?), go to create
        // Note: isReadyForPickup returns true for dipinjam if partial items exist.
        // But for lookup, if it's dipinjam, we might default to return? 
        // Or if partial, maybe show show page?
        // Let's default to show page if it's mixed, so user can choose.
        if ($peminjaman->status === 'dipinjam') {
             return redirect()->route('peminjaman.show', $peminjaman);
        }

        return redirect()->route('pengembalian.create', $peminjaman);
    }

    /**
     * Show the form for creating a new pengembalian.
     * Setiap unit harus diinspeksi kondisinya.
     */
    public function create(Peminjaman $peminjaman): View
    {
        // Hanya bisa mengembalikan peminjaman yang statusnya 'disetujui' atau 'dipinjam'
        if (!in_array($peminjaman->status, ['disetujui', 'dipinjam'])) {
            abort(403, 'Peminjaman ini tidak dalam status aktif.');
        }

        // Cek apakah sudah di-handover (sudah diserahkan ke peminjam)
        // Jika status disetujui dan belum ada handover sama sekali, harus handover dulu.
        if ($peminjaman->status === 'disetujui' && $peminjaman->isReadyForPickup()) {
            return redirect()->route('peminjaman.handover', $peminjaman)
                ->with('error', 'Barang belum diserahkan. Lakukan serah terima terlebih dahulu.');
        }

        // Cek apakah sudah ada pengembalian
        if ($peminjaman->pengembalian) {
            abort(403, 'Peminjaman ini sudah dikembalikan.');
        }

        $peminjaman->load([
            'user',
            'details.sarprasUnit.sarpras.checklistTemplates',
            'details.checklistHandover', // Load hasil checklist awal
            'details.sarprasUnit.lokasi',
        ]);

        return view('pengembalian.create', compact('peminjaman'));
    }

    /**
     * Store a newly created pengembalian in storage.
     * Proses inspeksi kondisi setiap unit.
     */
    public function store(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        // Validasi
        if (!in_array($peminjaman->status, ['disetujui', 'dipinjam'])) {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        if ($peminjaman->pengembalian) {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        // Build validation rules dynamically based on units
        $rules = [
            'tgl_kembali_aktual' => 'required|date',
        ];

        $messages = [
            'tgl_kembali_aktual.required' => 'Tanggal pengembalian wajib diisi.',
        ];

        foreach ($peminjaman->details as $detail) {
            $unitId = $detail->sarpras_unit_id;
            
            // Skip consumables (no unit id)
            if (!$unitId) continue;
            
            // Skip validation rules for unpicked items
            if (!$detail->handed_over_at) continue;

            $rules["kondisi_{$unitId}"] = 'required|in:baik,rusak_ringan,rusak_berat,hilang';
            $rules["catatan_{$unitId}"] = 'nullable|string|min:20|max:500';
            $rules["denda_{$unitId}"] = 'nullable|integer|min:0';
            $rules["foto_{$unitId}"] = 'nullable|image|max:2048';

            $messages["kondisi_{$unitId}.required"] = "Kondisi unit {$detail->sarprasUnit->kode_unit} wajib dipilih.";
        }

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // Buat record pengembalian header
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => auth()->id(),
                'tgl_kembali_aktual' => $validated['tgl_kembali_aktual'],
            ]);

            // Proses setiap unit
            foreach ($peminjaman->details as $detail) {
                // Skip consumables
                if (!$detail->sarpras_unit_id) continue;

                $unitId = $detail->sarpras_unit_id;
                $unit = $detail->sarprasUnit;

                // SKIP if unit was never handed over (not picked up)
                // We should reset its status to available
                if (!$detail->handed_over_at) {
                    $unit->update([
                        'status' => SarprasUnit::STATUS_TERSEDIA
                    ]);
                    // No pengembalian detail created for this
                    continue;
                }

                $kondisiAkhir = $validated["kondisi_{$unitId}"];

                // Upload foto jika ada
                $fotoPath = null;
                if ($request->hasFile("foto_{$unitId}")) {
                    $fotoPath = $request->file("foto_{$unitId}")
                        ->store('pengembalian', 'public');
                }

                // Buat detail pengembalian
                $pengembalianDetail = PengembalianDetail::create([
                    'pengembalian_id' => $pengembalian->id,
                    'sarpras_unit_id' => $unitId,
                    'kondisi_akhir' => $kondisiAkhir,
                    'foto_kondisi' => $fotoPath,
                    'catatan' => $validated["catatan_{$unitId}"] ?? null,
                    'denda' => $validated["denda_{$unitId}"] ?? null,
                ]);

                // Save checklist pengembalian data if exists
                if ($unit->sarpras && $unit->sarpras->checklistTemplates->isNotEmpty()) {
                    foreach ($unit->sarpras->checklistTemplates as $template) {
                        $checkKey = "checklist_{$detail->id}_{$template->id}";
                        $noteKey = "checklist_note_{$detail->id}_{$template->id}";
                        
                        \App\Models\ChecklistPengembalian::create([
                            'peminjaman_detail_id' => $detail->id,
                            'checklist_template_id' => $template->id,
                            'is_checked' => $request->has($checkKey) ? true : false,
                            'catatan' => $request->input($noteKey),
                        ]);
                    }
                }

                // Update status dan kondisi unit berdasarkan hasil inspeksi
                switch ($kondisiAkhir) {
                    case 'baik':
                        if ($unit->kondisi !== SarprasUnit::KONDISI_BAIK) {
                            \App\Models\UnitConditionLog::create([
                                'sarpras_unit_id' => $unit->id,
                                'kondisi_lama' => $unit->kondisi,
                                'kondisi_baru' => SarprasUnit::KONDISI_BAIK,
                                'keterangan' => 'Pengembalian: Barang kembali dalam kondisi baik. ' . ($validated["catatan_{$unitId}"] ?? ''),
                                'user_id' => $peminjaman->user_id, // Gunakan ID Peminjam, bukan Admin
                                'related_model_type' => get_class($pengembalian),
                                'related_model_id' => $pengembalian->id,
                            ]);
                        }
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_BAIK,
                            'status' => SarprasUnit::STATUS_TERSEDIA,
                        ]);
                        break;

                    case 'rusak_ringan':
                        if ($unit->kondisi !== SarprasUnit::KONDISI_RUSAK_RINGAN) {
                            \App\Models\UnitConditionLog::create([
                                'sarpras_unit_id' => $unit->id,
                                'kondisi_lama' => $unit->kondisi,
                                'kondisi_baru' => SarprasUnit::KONDISI_RUSAK_RINGAN,
                                'keterangan' => 'Pengembalian: ' . ($validated["catatan_{$unitId}"] ?? 'Rusak Ringan saat dikembalikan'),
                                'user_id' => $peminjaman->user_id, // Gunakan ID Peminjam, bukan Admin
                                'related_model_type' => get_class($pengembalian),
                                'related_model_id' => $pengembalian->id,
                            ]);
                        }
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_RUSAK_RINGAN,
                            'status' => SarprasUnit::STATUS_TERSEDIA, // Masih bisa dipakai
                        ]);
                        break;

                    case 'rusak_berat':
                        if ($unit->kondisi !== SarprasUnit::KONDISI_RUSAK_BERAT) {
                            \App\Models\UnitConditionLog::create([
                                'sarpras_unit_id' => $unit->id,
                                'kondisi_lama' => $unit->kondisi,
                                'kondisi_baru' => SarprasUnit::KONDISI_RUSAK_BERAT,
                                'keterangan' => 'Pengembalian: ' . ($validated["catatan_{$unitId}"] ?? 'Rusak Berat saat dikembalikan'),
                                'user_id' => $peminjaman->user_id, // Gunakan ID Peminjam, bukan Admin
                                'related_model_type' => get_class($pengembalian),
                                'related_model_id' => $pengembalian->id,
                            ]);
                        }
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_RUSAK_BERAT,
                            'status' => SarprasUnit::STATUS_MAINTENANCE,
                        ]);
                        break;

                    case 'hilang':
                        $unit->update([
                            'status' => SarprasUnit::STATUS_DIHAPUSBUKUKAN,
                        ]);

                        // Catat ke tabel barang hilang
                        BarangHilang::create([
                            'pengembalian_detail_id' => $pengembalianDetail->id,
                            'user_id' => $peminjaman->user_id,
                            'keterangan' => $validated["catatan_{$unitId}"] ?? 'Barang hilang saat dipinjam.',
                            'status' => BarangHilang::STATUS_BELUM_DIGANTI,
                        ]);
                        break;
                }
            }

            // Update status peminjaman menjadi selesai
            $peminjaman->update(['status' => 'selesai']);

            DB::commit();

            \App\Helpers\LogHelper::record('update', "Memproses pengembalian untuk peminjaman ID: {$peminjaman->id}");

            $totalDenda = $pengembalian->total_denda;
            $message = 'Pengembalian berhasil dicatat.';
            if ($totalDenda > 0) {
                $message .= ' Total denda: Rp '.number_format($totalDenda, 0, ',', '.');
            }

            return redirect()
                ->route('peminjaman.show', $peminjaman)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified pengembalian.
     */
    public function show(Pengembalian $pengembalian): View
    {
        $pengembalian->load([
            'peminjaman.user',
            'petugas',
            'details.sarprasUnit.sarpras',
        ]);

        return view('pengembalian.show', compact('pengembalian'));
    }
}
