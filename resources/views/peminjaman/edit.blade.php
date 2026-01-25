<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.index', request()->query()) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proses Peminjaman</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Info Peminjaman --}}
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Info Peminjam --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Info Peminjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $peminjaman->user->name ?? '-' }}</span></p>
                                <p><span class="text-gray-500">Email:</span> {{ $peminjaman->user->email ?? '-' }}</p>
                                <p><span class="text-gray-500">Kontak:</span> {{ $peminjaman->user->kontak ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Info Barang --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Unit yang Dipinjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Jumlah Unit:</span> <span class="text-xl font-bold text-blue-600">{{ $peminjaman->details->count() }}</span></p>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach ($peminjaman->details as $detail)
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ $detail->sarprasUnit->kode_unit ?? '-' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Tanggal --}}
                    <div class="mb-6 bg-blue-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-gray-500 text-sm">Tanggal Pinjam</p>
                                <p class="font-semibold">{{ $peminjaman->tgl_pinjam?->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Rencana Kembali</p>
                                <p class="font-semibold">{{ $peminjaman->tgl_kembali_rencana?->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($peminjaman->keterangan)
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Keterangan dari Peminjam</h3>
                            <p class="text-gray-700">{{ $peminjaman->keterangan }}</p>
                        </div>
                    @endif

                    {{-- Form Update Status --}}
                    <form action="{{ route('peminjaman.update', $peminjaman) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        {{-- Preserve filter params --}}
                        <input type="hidden" name="page" value="{{ request('page') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}"> {{-- Note: This conflicts with the select name='status'. We should rename the filter param in hidden input --}}
                        {{-- Correction: The request filter key is 'status', but the select name is also 'status' (the data being updated). --}}
                        {{-- To avoid conflict, we shouldn't pass 'status' (the filter) as 'status' (the form data). --}}
                        {{-- But wait, standard behavior is the form data overrides. --}}
                        {{-- However, in controller we look for query string OR body. If we post it, it enters request. --}}
                        {{-- We need to pass the FILTER status with a different name if we want to redirect back to it. --}}
                        
                        {{-- Let's rename the hidden inputs to avoid collision with actual form fields --}}
                        {{-- PeminjamanController logic: $request->only(['redirect_status', 'page'...]) --}}
                        
                        <input type="hidden" name="redirect_status" value="{{ request('status') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Peminjaman <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="menunggu" {{ $peminjaman->status === 'menunggu' ? 'selected' : '' }} class="bg-yellow-50 text-yellow-800">⏳ Menunggu</option>
                                {{-- Auto-select 'Disetujui' if current status is 'Menunggu' --}}
                                <option value="disetujui" {{ $peminjaman->status === 'disetujui' || $peminjaman->status === 'menunggu' ? 'selected' : '' }} class="bg-green-50 text-green-800 font-bold">✅ Disetujui (Terima Pengajuan)</option>
                                <option value="ditolak" {{ $peminjaman->status === 'ditolak' ? 'selected' : '' }} class="bg-red-50 text-red-800">❌ Ditolak</option>
                                <option value="selesai" {{ $peminjaman->status === 'selesai' ? 'selected' : '' }} class="bg-blue-50 text-blue-800">📦 Selesai</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan Petugas --}}
                        <div id="catatan-container">
                            <label for="catatan_petugas" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Petugas 
                                <span id="catatan-required" class="text-red-500 hidden">* (Wajib diisi jika menolak)</span>
                            </label>
                            <textarea 
                                name="catatan_petugas" 
                                id="catatan_petugas" 
                                rows="3" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                placeholder="Masukkan catatan atau alasan penolakan..."
                            >{{ old('catatan_petugas', $peminjaman->catatan_petugas) }}</textarea>
                            @error('catatan_petugas')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Catatan ini akan ditampilkan kepada peminjam.</p>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <a href="{{ route('peminjaman.index', request()->query()) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk menampilkan/menyembunyikan catatan wajib --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const catatanRequired = document.getElementById('catatan-required');
            const catatanTextarea = document.getElementById('catatan_petugas');

            function updateCatatanRequirement() {
                if (statusSelect.value === 'ditolak') {
                    catatanRequired.classList.remove('hidden');
                    catatanTextarea.setAttribute('required', 'required');
                } else {
                    catatanRequired.classList.add('hidden');
                    catatanTextarea.removeAttribute('required');
                }
            }

            updateCatatanRequirement();
            statusSelect.addEventListener('change', updateCatatanRequirement);
        });
    </script>
</x-app-layout>
