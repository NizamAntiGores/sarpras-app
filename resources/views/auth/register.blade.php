<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold text-gray-800">Daftar Akun</h2>
        <p class="text-sm text-gray-500">Sistem Informasi Sarpras SMK</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="registerForm()" @submit="handleSubmit">
        @csrf

        {{-- Step 1: Input NISN/NIP --}}
        <div x-show="step === 1" x-transition>
            {{-- Role Selection --}}
            <div class="mb-4">
                <x-input-label value="Saya adalah" />
                <div class="flex gap-4 mt-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="role" value="siswa" x-model="role" class="form-radio text-blue-600">
                        <span class="ml-2">Siswa</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="role" value="guru" x-model="role" class="form-radio text-purple-600">
                        <span class="ml-2">Guru</span>
                    </label>
                </div>
            </div>

            {{-- Nomor Induk --}}
            <div class="mb-4">
                <x-input-label for="nomor_induk" x-text="role === 'guru' ? 'NIP' : 'NISN'" />
                <x-text-input id="nomor_induk" class="block mt-1 w-full" type="text" name="nomor_induk"
                    x-model="nomorInduk"
                    x-bind:placeholder="role === 'guru' ? 'Masukkan NIP Anda' : 'Masukkan NISN Anda'"
                    required />
                <p class="mt-1 text-xs text-gray-500" x-text="role === 'guru' ? 'Contoh: 198001012005011001' : 'Contoh: 0012345678'"></p>
            </div>

            {{-- Error Message --}}
            <div x-show="errorMessage" x-transition class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                <span x-text="errorMessage"></span>
            </div>

            {{-- Check Button --}}
            <button type="button" @click="checkNomorInduk"
                :disabled="isLoading || !nomorInduk"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition">
                <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isLoading ? 'Memeriksa...' : 'Cek Data'"></span>
            </button>

            <p class="mt-4 text-center text-xs text-gray-500">
                Data Anda harus terdaftar di database sekolah.<br>
                Hubungi admin jika belum terdaftar.
            </p>
        </div>

        {{-- Step 2: Complete Registration --}}
        <div x-show="step === 2" x-transition>
            {{-- Success Message --}}
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm">Data ditemukan! Lengkapi informasi berikut.</span>
                </div>
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="role" :value="role">
            <input type="hidden" name="nomor_induk" :value="nomorInduk">

            {{-- Auto-filled Name (readonly) --}}
            <div class="mb-4">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" class="block mt-1 w-full bg-gray-100" type="text" name="name"
                    x-model="nama" readonly />
                <p class="mt-1 text-xs text-gray-500">Nama sesuai data sekolah (tidak bisa diubah)</p>
            </div>

            {{-- Auto-filled Kelas (readonly, only for siswa) --}}
            <div class="mb-4" x-show="role === 'siswa' && kelas">
                <x-input-label for="kelas" value="Kelas" />
                <x-text-input id="kelas" class="block mt-1 w-full bg-gray-100" type="text" name="kelas"
                    x-model="kelas" readonly />
            </div>

            {{-- NISN/NIP Display --}}
            <div class="mb-4">
                <x-input-label x-text="role === 'guru' ? 'NIP' : 'NISN'" />
                <x-text-input class="block mt-1 w-full bg-gray-100" type="text"
                    x-model="nomorInduk" readonly />
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                    :value="old('email')" required autocomplete="username" placeholder="email@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Kontak/No HP --}}
            <div class="mb-4">
                <x-input-label for="kontak" value="No. HP/WhatsApp (Opsional)" />
                <x-text-input id="kontak" class="block mt-1 w-full" type="text" name="kontak"
                    :value="old('kontak')" placeholder="08xxxxxxxxxx" />
                <x-input-error :messages="$errors->get('kontak')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="button" @click="step = 1; errorMessage = ''"
                    class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    ← Kembali
                </button>
                <x-primary-button class="flex-1 justify-center">
                    Daftar
                </x-primary-button>
            </div>
        </div>

        <div class="flex items-center justify-center mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                href="{{ route('login') }}">
                Sudah punya akun? Login
            </a>
        </div>
    </form>

    <script>
        function registerForm() {
            return {
                step: 1,
                role: 'siswa',
                nomorInduk: '',
                nama: '',
                kelas: '',
                isLoading: false,
                errorMessage: '',

                async checkNomorInduk() {
                    if (!this.nomorInduk) {
                        this.errorMessage = 'Masukkan NISN/NIP terlebih dahulu.';
                        return;
                    }

                    this.isLoading = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch('{{ route("whitelist.lookup") }}?nomor_induk=' + encodeURIComponent(this.nomorInduk));
                        const data = await response.json();

                        if (data.found) {
                            this.nama = data.data.nama;
                            this.kelas = data.data.kelas || '';
                            this.role = data.data.role;
                            this.step = 2;
                        } else {
                            this.errorMessage = data.message;
                        }
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                handleSubmit(e) {
                    if (this.step === 1) {
                        e.preventDefault();
                        this.checkNomorInduk();
                    }
                }
            }
        }
    </script>
</x-guest-layout>