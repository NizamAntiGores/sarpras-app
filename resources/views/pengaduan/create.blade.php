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
                    <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="judul" :value="__('Judul Pengaduan')" />
                            <x-text-input id="judul" class="block mt-1 w-full" type="text" name="judul" :value="old('judul')" required placeholder="Contoh: AC Bocor di Lab Komputer 1" />
                            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="lokasi_id" :value="__('Lokasi Kejadian')" />
                            <select id="lokasi_id" name="lokasi_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Pilih Lokasi</option>
                                @foreach($lokasi as $l)
                                    <option value="{{ $l->id }}" {{ old('lokasi_id') == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('lokasi_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sarpras_id" :value="__('Jenis Sarpras (Opsional)')" />
                            <select id="sarpras_id" name="sarpras_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" selected>Tidak Tahu / Lainnya</option>
                                @foreach($sarpras as $s)
                                    <option value="{{ $s->id }}" {{ old('sarpras_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_barang }} ({{ $s->kategori->nama_kategori ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Pilih jenis sarpras yang bermasalah jika ada di daftar.</p>
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
