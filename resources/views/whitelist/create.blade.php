<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Data Whitelist
            </h2>
            <a href="{{ route('whitelist.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition ease-in-out duration-150">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('whitelist.store') }}" x-data="{ role: 'siswa' }">
                        @csrf

                        {{-- Role Selection --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="siswa" x-model="role" class="form-radio text-blue-600">
                                    <span class="ml-2">Siswa</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="guru" x-model="role" class="form-radio text-purple-600">
                                    <span class="ml-2">Guru</span>
                                </label>
                            </div>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nomor Induk --}}
                        <div class="mb-4">
                            <label for="nomor_induk" class="block text-sm font-medium text-gray-700 mb-1">
                                <span x-text="role === 'guru' ? 'NIP' : 'NISN'">NISN</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_induk" id="nomor_induk" value="{{ old('nomor_induk') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :placeholder="role === 'guru' ? 'Masukkan NIP' : 'Masukkan NISN'" required>
                            @error('nomor_induk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Masukkan nama lengkap" required>
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kelas (hanya untuk siswa) --}}
                        <div class="mb-6" x-show="role === 'siswa'" x-transition>
                            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">
                                Kelas
                            </label>
                            <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Contoh: XII RPL 1">
                            @error('kelas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
