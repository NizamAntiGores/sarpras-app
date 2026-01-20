<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Peminjaman</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Status Badge --}}
                    <div class="mb-6 text-center">
                        @switch($peminjaman->status)
                            @case('menunggu')<span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-lg font-medium">⏳ Menunggu Persetujuan</span>@break
                            @case('disetujui')<span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-lg font-medium">✅ Disetujui</span>@break
                            @case('selesai')<span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-lg font-medium">📦 Selesai</span>@break
                            @case('ditolak')<span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-lg font-medium">❌ Ditolak</span>@break
                        @endswitch
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Info Peminjam --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Info Peminjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $peminjaman->user->name ?? '-' }}</span></p>
                                <p><span class="text-gray-500">Email:</span> {{ $peminjaman->user->email ?? '-' }}</p>
                                <p><span class="text-gray-500">Kontak:</span> {{ $peminjaman->user->kontak ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Info Barang (Unit-Based) --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Unit yang Dipinjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Jumlah Unit:</span> <span class="text-xl font-bold text-indigo-600">{{ $peminjaman->details->count() }}</span></p>
                                <div class="mt-2 space-y-1">
                                    @foreach ($peminjaman->details as $detail)
                                        <div class="flex items-center justify-between bg-white p-2 rounded border text-sm">
                                            <div>
                                                <span class="font-mono font-medium">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>
                                                <span class="text-gray-500 ml-2">{{ $detail->sarprasUnit->sarpras->nama_barang ?? '-' }}</span>
                                            </div>
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $detail->sarprasUnit->kondisi === 'baik' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ ucfirst(str_replace('_', ' ', $detail->sarprasUnit->kondisi ?? '-')) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Tanggal --}}
                    <div class="mt-6 bg-indigo-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 border-b border-indigo-200 pb-2">Info Peminjaman</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-gray-500 text-sm">Tanggal Pinjam</p>
                                <p class="font-semibold">{{ $peminjaman->tgl_pinjam?->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Rencana Kembali</p>
                                <p class="font-semibold">{{ $peminjaman->tgl_kembali_rencana?->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Diproses Oleh</p>
                                <p class="font-semibold">{{ $peminjaman->petugas->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($peminjaman->keterangan)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Keterangan dari Peminjam</h3>
                            <p class="text-gray-700">{{ $peminjaman->keterangan }}</p>
                        </div>
                    @endif

                    {{-- Catatan Petugas --}}
                    @if ($peminjaman->catatan_petugas)
                        <div class="mt-6 rounded-lg p-4 {{ $peminjaman->status === 'ditolak' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
                            <h3 class="font-semibold mb-2 {{ $peminjaman->status === 'ditolak' ? 'text-red-800' : 'text-yellow-800' }}">
                                {{ $peminjaman->status === 'ditolak' ? '❌ Alasan Penolakan' : '📝 Catatan dari Petugas' }}
                            </h3>
                            <p class="{{ $peminjaman->status === 'ditolak' ? 'text-red-700' : 'text-yellow-700' }}">{{ $peminjaman->catatan_petugas }}</p>
                        </div>
                    @endif

                    {{-- Info Pengembalian jika sudah selesai --}}
                    @if ($peminjaman->status === 'selesai' && $peminjaman->pengembalian)
                        <div class="mt-6 bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b border-blue-200 pb-2">Info Pengembalian</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm">Tanggal Kembali</p>
                                    <p class="font-medium">{{ $peminjaman->pengembalian->tgl_kembali_aktual?->format('d M Y') ?? $peminjaman->pengembalian->created_at?->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Diterima Oleh</p>
                                    <p class="font-medium">{{ $peminjaman->pengembalian->petugas->name ?? '-' }}</p>
                                </div>
                            </div>
                            
                            @if ($peminjaman->pengembalian->details && $peminjaman->pengembalian->details->count() > 0)
                                <div class="mt-4">
                                    <p class="text-gray-500 text-sm mb-2">Kondisi Unit Saat Dikembalikan:</p>
                                    <div class="space-y-1">
                                        @foreach ($peminjaman->pengembalian->details as $detail)
                                            <div class="flex items-center justify-between bg-white p-2 rounded border text-sm">
                                                <span class="font-mono">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-xs 
                                                    {{ $detail->kondisi_akhir === 'baik' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $detail->kondisi_akhir === 'rusak_ringan' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                    {{ $detail->kondisi_akhir === 'rusak_berat' ? 'bg-orange-100 text-orange-700' : '' }}
                                                    {{ $detail->kondisi_akhir === 'hilang' ? 'bg-red-100 text-red-700' : '' }}
                                                ">
                                                    {{ ucfirst(str_replace('_', ' ', $detail->kondisi_akhir)) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t flex flex-wrap justify-between items-center gap-3">
                        <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Kembali</a>
                        
                        <div class="flex gap-3">
                            @if (in_array(auth()->user()->role, ['admin', 'petugas']))
                                @if ($peminjaman->status === 'menunggu')
                                    <a href="{{ route('peminjaman.edit', $peminjaman) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Proses Peminjaman
                                    </a>
                                @elseif ($peminjaman->status === 'disetujui')
                                    <a href="{{ route('pengembalian.create', ['peminjaman' => $peminjaman->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Proses Pengembalian
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
