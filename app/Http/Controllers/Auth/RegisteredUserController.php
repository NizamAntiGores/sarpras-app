<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentWhitelist;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:guru,siswa'],
            'nomor_induk' => ['required', 'string', 'max:30', 'unique:' . User::class],
            'kelas' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'kontak' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Status (Guru/Siswa) wajib dipilih.',
            'role.in' => 'Status tidak valid.',
            'nomor_induk.required' => 'NIP/NISN wajib diisi.',
            'nomor_induk.unique' => 'NIP/NISN sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Validasi whitelist
        $whitelist = StudentWhitelist::findByNomorInduk($request->nomor_induk);
        
        if (!$whitelist) {
            return back()->withErrors([
                'nomor_induk' => 'NISN/NIP tidak ditemukan dalam database sekolah. Hubungi admin untuk didaftarkan.',
            ])->withInput();
        }

        if ($whitelist->is_registered) {
            return back()->withErrors([
                'nomor_induk' => 'NISN/NIP ini sudah terdaftar. Silakan login dengan akun yang sudah ada.',
            ])->withInput();
        }

        // Gunakan nama dari whitelist (bukan input user) untuk keamanan
        $user = User::create([
            'name' => $whitelist->nama, // Nama dari whitelist
            'email' => $request->email,
            'kontak' => $request->kontak,
            'password' => $request->password,
            'role' => $whitelist->role, // Role dari whitelist
            'nomor_induk' => $request->nomor_induk,
            'kelas' => $whitelist->kelas, // Kelas dari whitelist
        ]);

        // Tandai whitelist sebagai sudah terdaftar
        $whitelist->markAsRegistered();

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
