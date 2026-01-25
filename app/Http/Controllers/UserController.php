<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::orderBy('created_at', 'desc');

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search Name/Email/Nomor Induk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     * Admin dapat memilih role user (admin, petugas, peminjam)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nomor_induk' => 'nullable|string|max:50|unique:users,nomor_induk',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,petugas,peminjam',
            'kontak' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        User::create([
            'name' => $validated['name'],
            'nomor_induk' => $validated['nomor_induk'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'kontak' => $validated['kontak'] ?? null,
            'email_verified_at' => now(),
        ]);

        \App\Helpers\LogHelper::record('create', "Menambahkan user baru: {$validated['name']} ({$validated['role']})");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     * Admin dapat mengubah role user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nomor_induk' => 'nullable|string|max:50|unique:users,nomor_induk,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,petugas,peminjam',
            'kontak' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_induk.unique' => 'Nomor Induk sudah digunakan user lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan user lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'nomor_induk' => $validated['nomor_induk'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'kontak' => $validated['kontak'] ?? null,
        ]);

        \App\Helpers\LogHelper::record('update', "Memperbarui data user: {$user->name}");

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $deletedName = $user->name;
        $user->delete();

        \App\Helpers\LogHelper::record('delete', "Menghapus user: {$deletedName}");

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
