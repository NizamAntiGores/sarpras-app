<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengaduan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" 
                          x-data="{ 
                              jenis: '{{ old('jenis', '') }}',
                              lokasiPilihan: '{{ old('lokasi_id') ? 'list' : (old('lokasi_lainnya') ? 'lainnya' : '') }}',
                              barangPilihan: '{{ old('sarpras_id') ? 'list' : (old('barang_lainnya') ? 'lainnya' : '') }}'
                          }">
                        @csrf
                        
                        {{-- Jenis Pengaduan --}}
                        <div>
                            <x-input-label :value="__('Jenis Pengaduan')" />
                            <div class="mt-2 grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="jenis" value="tempat" x-model="jenis" class="peer sr-only" {{ old('jenis') == 'tempat' ? 'checked' : '' }} required>
                                    <div class="p-4 border-2 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition">
                                        <div class="text-3xl mb-2">🏫</div>
                                        <div class="font-semibold text-gray-700">Tempat / Ruangan</div>
                                        <p class="text-xs text-gray-500 mt-1">AC rusak, pintu rusak, dll</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="jenis" value="barang" x-model="jenis" class="peer sr-only" {{ old('jenis') == 'barang' ? 'checked' : '' }}>
                                    <div class="p-4 border-2 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition">
                                        <div class="text-3xl mb-2">📦</div>
                                        <div class="font-semibold text-gray-700">Barang / Sarpras</div>
                                        <p class="text-xs text-gray-500 mt-1">Proyektor, komputer, dll</p>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="judul" :value="__('Judul Pengaduan')" />
                            <x-text-input id="judul" class="block mt-1 w-full" type="text" name="judul" :value="old('judul')" required placeholder="Contoh: AC Bocor di Lab Komputer 1" />
                            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                        </div>

                        {{-- Lokasi - Show when jenis = tempat --}}
                        <div x-show="jenis === 'tempat'" x-transition class="space-y-3">
                            <x-input-label :value="__('Lokasi Kejadian')" />
                            
                            {{-- Pilihan: Dari Daftar atau Lainnya --}}
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" x-model="lokasiPilihan" value="list" class="text-blue-600">
                                    <span class="ml-2 text-sm">Pilih dari daftar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="lokasiPilihan" value="lainnya" class="text-blue-600">
                                    <span class="ml-2 text-sm">Lainnya (tulis manual)</span>
                                </label>
                            </div>

                            {{-- Dropdown Lokasi --}}
                            <div x-show="lokasiPilihan === 'list'">
                                <select id="lokasi_id" name="lokasi_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled selected>Pilih Lokasi</option>
                                    @foreach($lokasi as $l)
                                        <option value="{{ $l->id }}" {{ old('lokasi_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Input Manual Lokasi --}}
                            <div x-show="lokasiPilihan === 'lainnya'">
                                <x-text-input name="lokasi_lainnya" class="block w-full" type="text" :value="old('lokasi_lainnya')" placeholder="Contoh: Toilet lantai 2 gedung B" />
                            </div>

                            <x-input-error :messages="$errors->get('lokasi_id')" class="mt-2" />
                        </div>

                        {{-- Barang - Show when jenis = barang --}}
                        <div x-show="jenis === 'barang'" x-transition class="space-y-3">
                            <x-input-label :value="__('Barang yang Bermasalah')" />
                            
                            {{-- Pilihan: Dari Daftar atau Lainnya --}}
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" x-model="barangPilihan" value="list" class="text-blue-600">
                                    <span class="ml-2 text-sm">Pilih dari daftar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="barangPilihan" value="lainnya" class="text-blue-600">
                                    <span class="ml-2 text-sm">Lainnya (tulis manual)</span>
                                </label>
                            </div>

                            {{-- Dropdown Barang --}}
                            <div x-show="barangPilihan === 'list'">
                                <select id="sarpras_id" name="sarpras_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled selected>Pilih Barang</option>
                                    @foreach($sarpras as $s)
                                        <option value="{{ $s->id }}" {{ old('sarpras_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama_barang }} ({{ $s->kategori?->nama_kategori ?? 'Tanpa Kategori' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Input Manual Barang --}}
                            <div x-show="barangPilihan === 'lainnya'">
                                <x-text-input name="barang_lainnya" class="block w-full" type="text" :value="old('barang_lainnya')" placeholder="Contoh: Lampu neon di koridor" />
                            </div>

                            <x-input-error :messages="$errors->get('sarpras_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="deskripsi" :value="__('Deskripsi Masalah')" />
                            <textarea id="deskripsi" name="deskripsi" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="Jelaskan detail kerusakan atau permasalahan secara rinci...">{{ old('deskripsi') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="foto" :value="__('Foto Bukti (Opsional)')" />
                            <input id="foto" type="file" name="foto" accept="image/*" class="block mt-1 w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100" />
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Maks: 2MB.</p>
                            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('pengaduan.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Kirim Pengaduan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
