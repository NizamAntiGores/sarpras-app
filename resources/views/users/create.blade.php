<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User Baru</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div id="div-nomor-induk">
                                <label for="nomor_induk" id="label-nomor-induk"
                                    class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk</label>
                                <input type="text" name="nomor_induk" id="nomor_induk" value="{{ old('nomor_induk') }}"
                                    placeholder="NIS/NISN"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div id="div-kelas">
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}"
                                    placeholder="Contoh: XII RPL 1"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                        class="text-red-500">*</span></label>
                                <select name="role" id="role"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas
                                    </option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="kontak" class="block text-sm font-medium text-gray-700 mb-1">Kontak/No
                                HP</label>
                            <input type="text" name="kontak" id="kontak" value="{{ old('kontak') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password
                                    <span class="text-red-500">*</span></label>
                                <input type="password" name="password" id="password"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('users.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const roleSelect = document.getElementById('role');
                            const divKelas = document.getElementById('div-kelas');
                            const divNomorInduk = document.getElementById('div-nomor-induk');
                            const labelNomorInduk = document.getElementById('label-nomor-induk');
                            const inputNomorInduk = document.getElementById('nomor_induk');

                            function updateFields() {
                                const role = roleSelect.value;

                                // Reset classes
                                divKelas.classList.remove('hidden');
                                divNomorInduk.classList.remove('hidden');

                                if (role === 'siswa') {
                                    divKelas.style.display = 'block';
                                    labelNomorInduk.innerHTML = 'NIS / NISN <span class="text-red-500">*</span>';
                                    inputNomorInduk.placeholder = 'Masukkan NIS Siswa';
                                } else if (role === 'guru') {
                                    divKelas.style.display = 'none';
                                    labelNomorInduk.innerHTML = 'NIP <span class="text-red-500">*</span>';
                                    inputNomorInduk.placeholder = 'Masukkan NIP Guru';
                                } else {
                                    // Admin / Petugas
                                    divKelas.style.display = 'none';
                                    labelNomorInduk.innerHTML = 'Nomor Induk <span class="text-gray-400">(Opsional)</span>';
                                    inputNomorInduk.placeholder = 'Nomor Induk (jika ada)';
                                }
                            }

                            roleSelect.addEventListener('change', updateFields);
                            updateFields(); // Run on load
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>