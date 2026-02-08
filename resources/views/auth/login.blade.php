<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold text-gray-800">Login</h2>
        <p class="text-sm text-gray-500">Sistem Informasi Sarpras SMK</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert for Invalid Credentials -->
    @if ($errors->has('nomor_induk') || $errors->has('password'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Login Gagal!</strong>
            <span class="block sm:inline">{{ $errors->first('nomor_induk') ?: 'NISN/NIP atau password salah.' }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- NISN/NIP -->
        <div>
            <x-input-label for="nomor_induk" value="NISN / NIP" />
            <x-text-input id="nomor_induk" class="block mt-1 w-full" type="text" name="nomor_induk" :value="old('nomor_induk')" required
                autofocus autocomplete="username" placeholder="Masukkan NISN atau NIP" />
            <x-input-error :messages="$errors->get('nomor_induk')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>



        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                Masuk
            </x-primary-button>
        </div>
    </form>

    <!-- Link ke Register -->
    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
        <p class="text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Daftar di sini
            </a>
        </p>
    </div>
</x-guest-layout>