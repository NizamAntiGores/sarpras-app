<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Serah Terima Barang</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Main Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Header with Status --}}
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-800 rounded-full text-lg font-medium">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Siap Untuk Diserahkan
                        </div>
                        <p class="text-gray-500 mt-2">Konfirmasi serah terima barang kepada peminjam</p>
                    </div>

                    {{-- QR Code Display --}}
                    <div class="flex justify-center mb-6">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 text-center">
                            <div class="bg-white p-4 rounded-lg shadow-md inline-block">
                                {!! QrCode::size(150)->generate($peminjaman->qr_code) !!}
                            </div>
                            <p class="mt-3 font-mono text-lg font-bold text-blue-800">{{ $peminjaman->qr_code }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Info Peminjam --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Peminjam
                            </h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $peminjaman->user->name ?? '-' }}</span></p>
                                <p><span class="text-gray-500">Email:</span> {{ $peminjaman->user->email ?? '-' }}</p>
                                <p><span class="text-gray-500">Kontak:</span> {{ $peminjaman->user->kontak ?? '-' }}</p>
                                @if($peminjaman->user->kelas)
                                    <p><span class="text-gray-500">Kelas:</span> {{ $peminjaman->user->kelas }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Info Peminjaman --}}
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b border-blue-200 pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Info Peminjaman
                            </h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Tanggal Pinjam:</span> <span class="font-medium">{{ $peminjaman->tgl_pinjam?->format('d M Y') }}</span></p>
                                <p><span class="text-gray-500">Rencana Kembali:</span> <span class="font-medium">{{ $peminjaman->tgl_kembali_rencana?->format('d M Y') }}</span></p>
                                <p><span class="text-gray-500">Disetujui Oleh:</span> {{ $peminjaman->petugas->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Barang yang Akan Diserahkan --}}
                    <div class="mt-6 bg-green-50 rounded-lg p-4 border border-green-200">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Barang yang Akan Diserahkan ({{ $peminjaman->details->count() }} unit)
                        </h3>
                        
                        @php
                            $unitsByLocation = $peminjaman->details->groupBy(function($detail) {
                                return $detail->sarprasUnit?->lokasi?->nama_lokasi ?? 'Lokasi Tidak Diketahui';
                            });
                        @endphp

                        <div class="space-y-4">
                            @foreach($unitsByLocation as $lokasi => $units)
                                <div class="bg-white rounded-lg p-4 border border-green-100">
                                    <p class="font-medium text-green-800 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $lokasi }}
                                    </p>
                                    <div class="space-y-2">
                                        @foreach($units as $detail)
                                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" checked disabled class="h-5 w-5 text-green-600 rounded border-gray-300">
                                                    <div>
                                                        <span class="font-mono font-medium text-gray-800">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>
                                                        <span class="text-gray-500 ml-2">{{ $detail->sarprasUnit?->sarpras?->nama_barang ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-1 rounded-full text-xs {{ $detail->sarprasUnit->kondisi === 'baik' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $detail->sarprasUnit->kondisi ?? '-')) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Keterangan Peminjaman --}}
                    @if($peminjaman->keterangan)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Keterangan dari Peminjam</h3>
                            <p class="text-gray-700">{{ $peminjaman->keterangan }}</p>
                        </div>
                    @endif

                    {{-- Confirmation Section --}}
                    <div class="mt-8 p-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border-2 border-amber-300">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-amber-800">Konfirmasi Serah Terima</h3>
                            <p class="text-amber-600 text-sm mt-1">Pastikan semua barang sudah diperiksa kondisinya sebelum diserahkan</p>
                        </div>
                        
                        <form action="{{ route('peminjaman.handover.process', $peminjaman) }}" method="POST" class="text-center">
                            @csrf
                            
                            <div class="bg-white rounded-lg p-4 mb-4 text-left">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" id="confirm_handover" required class="h-5 w-5 mt-0.5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                    <span class="text-gray-700">Saya telah memeriksa dan menyerahkan <strong>{{ $peminjaman->details->count() }} unit</strong> barang kepada <strong>{{ $peminjaman->user->name }}</strong></span>
                                </label>
                            </div>

                            <div class="flex justify-center gap-4">
                                <a href="{{ route('peminjaman.show', $peminjaman) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition">
                                    Batal
                                </a>
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Konfirmasi Serah Terima
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
