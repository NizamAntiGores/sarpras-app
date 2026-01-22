<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Status Pengaduan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Judul Pengaduan:</h3>
                        <p class="text-lg font-bold text-gray-900">{{ $pengaduan->judul }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $pengaduan->deskripsi }}</p>
                    </div>

                    <form action="{{ route('pengaduan.update', $pengaduan) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="status" :value="__('Status Pengaduan')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="belum_ditindaklanjuti" {{ $pengaduan->status == 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                                <option value="sedang_diproses" {{ $pengaduan->status == 'sedang_diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="selesai" {{ $pengaduan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditutup" {{ $pengaduan->status == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="catatan_petugas" :value="__('Catatan Tindak Lanjut')" />
                            <textarea id="catatan_petugas" name="catatan_petugas" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Berikan catatan mengenai tindakan yang diambil...">{{ old('catatan_petugas', $pengaduan->catatan_petugas) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Catatan ini dapat dilihat oleh pelapor.</p>
                            <x-input-error :messages="$errors->get('catatan_petugas')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('pengaduan.show', $pengaduan) }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
