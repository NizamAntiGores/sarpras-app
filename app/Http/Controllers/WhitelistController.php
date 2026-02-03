<?php

namespace App\Http\Controllers;

use App\Models\StudentWhitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhitelistController extends Controller
{
    /**
     * Tampilkan halaman daftar whitelist
     */
    public function index(Request $request)
    {
        $query = StudentWhitelist::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status registrasi
        if ($request->filled('status')) {
            $query->where('is_registered', $request->status === 'registered');
        }

        $whitelists = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('whitelist.index', compact('whitelists'));
    }

    /**
     * Tampilkan form import CSV
     */
    public function importForm()
    {
        return view('whitelist.import');
    }

    /**
     * Proses import dari CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'role' => 'required|in:siswa,guru',
        ], [
            'file.required' => 'File CSV wajib diupload.',
            'file.mimes' => 'File harus berformat CSV.',
            'file.max' => 'Ukuran file maksimal 2MB.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $file = $request->file('file');
        $role = $request->role;
        
        $handle = fopen($file->getPathname(), 'r');
        
        // Auto-detect delimiter (comma or semicolon)
        $firstLine = fgets($handle);
        rewind($handle);
        
        // Check which delimiter is used
        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }
        
        // Skip header row
        $header = fgetcsv($handle, 1000, $delimiter);
        
        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            // Pastikan row memiliki minimal 2 kolom (nomor_induk, nama)
            if (count($row) < 2) {
                $skipped++;
                continue;
            }

            $nomorInduk = trim($row[0]);
            $nama = trim($row[1]);
            $kelas = isset($row[2]) ? trim($row[2]) : null;

            // Validasi data
            if (empty($nomorInduk) || empty($nama)) {
                $skipped++;
                continue;
            }

            // Cek apakah sudah ada
            $existing = StudentWhitelist::where('nomor_induk', $nomorInduk)->first();
            if ($existing) {
                $skipped++;
                continue;
            }

            // Insert data
            StudentWhitelist::create([
                'nomor_induk' => $nomorInduk,
                'nama' => $nama,
                'kelas' => $kelas,
                'role' => $role,
                'is_registered' => false,
            ]);

            $imported++;
        }

        fclose($handle);

        return redirect()->route('whitelist.index')
            ->with('success', "Berhasil import {$imported} data. {$skipped} data dilewati (duplikat/tidak valid).");
    }

    /**
     * Form tambah manual
     */
    public function create()
    {
        return view('whitelist.create');
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_induk' => 'required|string|max:30|unique:student_whitelists,nomor_induk',
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:20',
            'role' => 'required|in:siswa,guru',
        ], [
            'nomor_induk.required' => 'NISN/NIP wajib diisi.',
            'nomor_induk.unique' => 'NISN/NIP sudah terdaftar di whitelist.',
            'nama.required' => 'Nama wajib diisi.',
        ]);

        StudentWhitelist::create([
            'nomor_induk' => $request->nomor_induk,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'role' => $request->role,
            'is_registered' => false,
        ]);

        return redirect()->route('whitelist.index')
            ->with('success', 'Data berhasil ditambahkan ke whitelist.');
    }

    /**
     * Hapus data whitelist
     */
    public function destroy(StudentWhitelist $whitelist)
    {
        if ($whitelist->is_registered) {
            return back()->with('error', 'Tidak bisa menghapus data yang sudah terdaftar sebagai user.');
        }

        $whitelist->delete();

        return back()->with('success', 'Data berhasil dihapus dari whitelist.');
    }

    /**
     * API: Lookup NISN/NIP untuk auto-fill di form registrasi
     */
    public function lookup(Request $request)
    {
        $nomorInduk = $request->nomor_induk;

        if (empty($nomorInduk)) {
            return response()->json([
                'found' => false,
                'message' => 'Nomor induk tidak boleh kosong.',
            ]);
        }

        $whitelist = StudentWhitelist::findByNomorInduk($nomorInduk);

        if (!$whitelist) {
            return response()->json([
                'found' => false,
                'message' => 'NISN/NIP tidak ditemukan dalam database sekolah. Hubungi admin untuk didaftarkan.',
            ]);
        }

        if ($whitelist->is_registered) {
            return response()->json([
                'found' => false,
                'message' => 'NISN/NIP ini sudah terdaftar. Silakan login dengan akun yang sudah ada.',
            ]);
        }

        return response()->json([
            'found' => true,
            'data' => [
                'nama' => $whitelist->nama,
                'kelas' => $whitelist->kelas,
                'role' => $whitelist->role,
            ],
        ]);
    }

    /**
     * Download template CSV
     */
    public function downloadTemplate(Request $request)
    {
        $role = $request->get('role', 'siswa');
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=template_{$role}.csv",
        ];

        if ($role === 'guru') {
            $content = "NIP,Nama\n";
            $content .= "198001012005011001,Pak Joko\n";
            $content .= "198502022010012002,Bu Siti\n";
        } else {
            $content = "NISN,Nama,Kelas\n";
            $content .= "0012345678,Ahmad Rizky,XII RPL 1\n";
            $content .= "0012345679,Sarah Putri,XII RPL 2\n";
        }

        return response($content, 200, $headers);
    }
}
