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
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Gagal!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

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

                    {{-- Form Serah Terima --}}
                    <form action="{{ route('peminjaman.handover.process', $peminjaman) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyerahkan barang-barang terpilih? Pastikan barang sudah fisik diserahkan.');">
                        @csrf
                        
                        {{-- Daftar Barang yang Akan Diserahkan --}}
                        <div class="mt-6 bg-green-50 rounded-lg p-4 border border-green-200">
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Barang yang Akan Diserahkan ({{ $peminjaman->details->count() }} unit)
                            </h3>
                            
                            @php
                                // Group logic: Assets by Location, Consumables separate
                                $groupedDetails = $peminjaman->details->groupBy(function($detail) {
                                    if ($detail->sarpras_unit_id && $detail->sarprasUnit) {
                                        return $detail->sarprasUnit->lokasi->nama_lokasi ?? 'Lokasi Tidak Diketahui';
                                    }
                                    return 'Bahan Habis Pakai / Lainnya';
                                });
                            @endphp

                            <div class="space-y-6">
                                @foreach($groupedDetails as $lokasi => $details)
                                    <div class="bg-white rounded-lg p-4 border border-green-100 shadow-sm relative">
                                         {{-- Location Header --}}
                                        <div class="flex justify-between items-center mb-3">
                                            <p class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $lokasi }}
                                            </p>
                                            
                                            {{-- Select All for this Location --}}
                                            <label class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 cursor-pointer select-none">
                                                <input type="checkbox" onchange="toggleLocation(this)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                                Pilih Semua di Sini
                                            </label>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($details as $detail)
                                                @php
                                                    $isHandedOver = !is_null($detail->handed_over_at);
                                                @endphp
                                                <div class="flex items-center justify-between p-3 rounded-lg border transition {{ $isHandedOver ? 'bg-gray-100 border-gray-200' : 'bg-white border-gray-200 hover:border-blue-300' }}">
                                                    <div class="flex items-center gap-4">
                                                        {{-- Checkbox --}}
                                                        @if(!$isHandedOver)
                                                            <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" 
                                                                   class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500 item-checkbox">
                                                        @else
                                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        @endif

                                                        <div>
                                                            @if($detail->sarpras_unit_id)
                                                                <span class="font-mono font-bold text-gray-800">{{ $detail->sarprasUnit->kode_unit }}</span>
                                                                <span class="text-gray-600 ml-2">{{ $detail->sarprasUnit->sarpras->nama_barang }}</span>
                                                            @else
                                                                <span class="font-bold text-gray-800">{{ $detail->sarpras->nama_barang }}</span>
                                                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded ml-2">{{ $detail->quantity }} Unit</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Status Badge --}}
                                                    <div>
                                                        @if($isHandedOver)
                                                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">
                                                                Sudah Diserahkan
                                                                <br>
                                                                <span class="text-[10px] text-green-600">{{ \Carbon\Carbon::parse($detail->handed_over_at)->format('H:i') }}</span>
                                                            </span>
                                                        @else
                                                            @if($detail->sarpras_unit_id)
                                                                <span class="px-2 py-1 rounded-full text-xs {{ $detail->sarprasUnit->kondisi === 'baik' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $detail->sarprasUnit->kondisi ?? '-')) }}
                                                                </span>
                                                            @else
                                                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">Habis Pakai</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Keterangan Peminjaman --}}
                        @if($peminjaman->keterangan)
                            <div class="mt-6 bg-gray-50 rounded-lg p-4 mx-1">
                                <h3 class="font-semibold text-gray-800 mb-2">Keterangan dari Peminjam</h3>
                                <p class="text-gray-700">{{ $peminjaman->keterangan }}</p>
                            </div>
                        @endif

                        {{-- Confirmation Section --}}
                        <div class="mt-8 p-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border-2 border-amber-300 sticky bottom-4 shadow-lg z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-amber-800">Konfirmasi Serah Terima</h3>
                                    <p class="text-amber-600 text-sm">Centang barang yang fisik-nya Anda serahkan saat ini.</p>
                                </div>
                                <div class="flex gap-3">
                                    <a href="{{ route('peminjaman.show', $peminjaman) }}" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition shadow-sm">
                                        Kembali
                                    </a>
                                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-md flex items-center gap-2 transform active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Serahkan Barang Terpilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <script>
                        function toggleLocation(sourceCheckbox) {
                            // Find parent container
                            const container = sourceCheckbox.closest('.bg-white');
                            // Find all item checkboxes inside
                            const checkboxes = container.querySelectorAll('.item-checkbox');
                            
                            checkboxes.forEach(cb => {
                                cb.checked = sourceCheckbox.checked;
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
