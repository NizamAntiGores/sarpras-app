<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ajukan Peminjaman Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    
                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-3 text-sm text-red-700">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info Card --}}
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                                <p class="mt-1 text-sm text-blue-700">
                                    Setelah mengajukan peminjaman, tunggu persetujuan dari petugas. 
                                    Anda dapat memantau status peminjaman di halaman daftar peminjaman.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Pilih Barang --}}
                        <div>
                            <label for="sarpras_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Barang <span class="text-red-500">*</span>
                            </label>
                            <select name="sarpras_id" id="sarpras_id" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sarpras_id') border-red-500 @enderror">
                                <option value="">-- Pilih Barang yang Ingin Dipinjam --</option>
                                @foreach ($sarpras as $item)
                                    <option value="{{ $item->id }}" {{ old('sarpras_id') == $item->id ? 'selected' : '' }}
                                            data-stok="{{ $item->stok }}">
                                        {{ $item->nama_barang }} ({{ $item->kode_barang }}) - Stok: {{ $item->stok }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sarpras_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($sarpras->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">Tidak ada barang yang tersedia untuk dipinjam saat ini.</p>
                            @endif
                        </div>

                        {{-- Jumlah Pinjam --}}
                        <div>
                            <label for="jumlah_pinjam" class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah Pinjam <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" 
                                   value="{{ old('jumlah_pinjam', 1) }}" min="1"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('jumlah_pinjam') border-red-500 @enderror">
                            @error('jumlah_pinjam')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Pinjam & Kembali --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tgl_pinjam" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Pinjam <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tgl_pinjam" id="tgl_pinjam" 
                                       value="{{ old('tgl_pinjam', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tgl_pinjam') border-red-500 @enderror">
                                @error('tgl_pinjam')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="tgl_kembali_rencana" class="block text-sm font-medium text-gray-700 mb-1">
                                    Rencana Tanggal Kembali <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tgl_kembali_rencana" id="tgl_kembali_rencana" 
                                       value="{{ old('tgl_kembali_rencana') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tgl_kembali_rencana') border-red-500 @enderror">
                                @error('tgl_kembali_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Keterangan/Alasan --}}
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                Alasan/Keperluan Peminjaman
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                      placeholder="Jelaskan keperluan peminjaman barang..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('peminjaman.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150"
                                    {{ $sarpras->isEmpty() ? 'disabled' : '' }}>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Ajukan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
