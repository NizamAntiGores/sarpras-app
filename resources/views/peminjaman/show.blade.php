<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.index', request()->query()) }}" class="text-gray-500 hover:text-gray-700 mr-3">
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
                <div class="p-6 print:p-0">
                    {{-- Print Header --}}
                    <div class="hidden print:block text-center mb-8 border-b-2 border-black pb-4">
                        <h1 class="text-2xl font-bold uppercase tracking-wider">Bukti Peminjaman Barang</h1>
                        <p class="text-sm">SMK Negeri 1 (Contoh) - Sistem Sarpras</p>
                    </div>
                    {{-- Status Badge --}}
                    <div class="mb-6 text-center">
                        @switch($peminjaman->status)
                            @case('menunggu')
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-lg font-medium print:hidden">⏳ Menunggu Persetujuan</span>
                                <span class="hidden print:block text-lg font-bold border border-black px-4 py-1 inline-block">STATUS: MENUNGGU PERSETUJUAN</span>
                                <p class="text-sm text-gray-500 mt-2">Kode QR akan muncul setelah pengajuan disetujui oleh petugas.</p>
                                @break
                            @case('disetujui')
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-lg font-medium print:hidden">✅ Disetujui</span>
                                <span class="hidden print:block text-lg font-bold border border-black px-4 py-1 inline-block">STATUS: DISETUJUI</span>
                                @if ($peminjaman->tgl_kembali_rencana && now()->gt($peminjaman->tgl_kembali_rencana))
                                    <div class="mt-2">
                                        <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-600 text-white shadow">
                                            ⚠️ Terlambat {{ now()->diffInDays($peminjaman->tgl_kembali_rencana) }} Hari
                                        </span>
                                    </div>
                                @endif
                                @break
                            @case('selesai')
                                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-lg font-medium print:hidden">📦 Selesai</span>
                                <span class="hidden print:block text-lg font-bold border border-black px-4 py-1 inline-block">STATUS: SELESAI</span>
                                @if ($peminjaman->pengembalian && $peminjaman->tgl_kembali_rencana)
                                    @php
                                        $tglAktual = \Carbon\Carbon::parse($peminjaman->pengembalian->tgl_kembali_aktual);
                                        $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_rencana);
                                        $isLate = $tglAktual->gt($tglRencana);
                                        $daysLate = $tglAktual->diffInDays($tglRencana);
                                    @endphp
                                    @if ($isLate && $daysLate > 0)
                                        <div class="mt-2">
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                ⚠️ Dikembalikan Terlambat {{ $daysLate }} Hari
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-green-50 text-green-600 border border-green-200">
                                                ✅ Tepat Waktu
                                            </span>
                                        </div>
                                    @endif
                                @endif
                                @break
                            @case('ditolak')
                                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-lg font-medium">❌ Ditolak</span>
                                @break
                        @endswitch
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2 print:gap-4">
                        {{-- Info Peminjam --}}
                        <div class="bg-gray-50 rounded-lg p-4 print:bg-white print:border print:border-black print:rounded-none">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Info Peminjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $peminjaman->user->name ?? '-' }}</span></p>
                                <p><span class="text-gray-500">Email:</span> {{ $peminjaman->user->email ?? '-' }}</p>
                                <p><span class="text-gray-500">Kontak:</span> {{ $peminjaman->user->kontak ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Info Barang (Unit-Based) --}}
                        <div class="bg-gray-50 rounded-lg p-4 print:bg-white print:border print:border-black print:rounded-none">
                            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Unit yang Dipinjam</h3>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Jumlah Unit:</span> <span class="text-xl font-bold text-blue-600">{{ $peminjaman->details->count() }}</span></p>
                                <div class="mt-2 space-y-1">
                                    @foreach ($peminjaman->details as $detail)
                                        <div class="flex items-center justify-between bg-white p-2 rounded border text-sm">
                                            <div>
                                                <span class="font-mono font-medium">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>
                                                <span class="text-gray-500 ml-2">{{ $detail->sarprasUnit?->sarpras?->nama_barang ?? '-' }}</span>
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
                    <div class="mt-6 bg-blue-50 rounded-lg p-4 print:bg-white print:border print:border-black print:mt-4 print:rounded-none">
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
                            @if (Str::contains($peminjaman->keterangan, '[REQ-EXT]'))
                                @php
                                    $parts = explode('| [REQ-EXT] ', $peminjaman->keterangan);
                                    $originalNote = $parts[0] ?? '';
                                    $extensionNote = $parts[1] ?? '';
                                @endphp
                                <p class="text-gray-700 mb-2">{{ $originalNote }}</p>
                                @if ($extensionNote)
                                    <div class="bg-purple-50 border-l-4 border-purple-500 p-3 rounded">
                                        <p class="text-sm font-bold text-purple-800 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Riwayat Perpanjangan:
                                        </p>
                                        <p class="text-sm text-purple-700 mt-1">{{ $extensionNote }}</p>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-700">{{ $peminjaman->keterangan }}</p>
                            @endif
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

                    {{-- QR Code Bukti Peminjaman (hanya jika disetujui DAN hanya untuk peminjam yang bersangkutan) --}}
                    @if ($peminjaman->status === 'disetujui' && $peminjaman->qr_code && auth()->id() === $peminjaman->user_id)
                        <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 print:bg-white print:border-none print:shadow-none print:p-0 print:mt-4">
                            <h3 class="font-semibold text-gray-800 mb-4 text-center">📱 QR Code Bukti Peminjaman</h3>
                            <div class="flex flex-col items-center">
                                <div class="bg-white p-4 rounded-lg shadow-md print:shadow-none print:p-0">
                                    {!! QrCode::size(180)->generate($peminjaman->qr_code) !!}
                                </div>
                                <p class="mt-3 font-mono text-lg font-bold text-blue-800">{{ $peminjaman->qr_code }}</p>
                                <p class="text-sm text-gray-500 mt-2 text-center">Tunjukkan QR code ini saat pengembalian barang</p>
                                <button onclick="window.print()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2 print:hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Cetak Bukti
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Kode QR untuk Admin/Petugas (hanya menampilkan kode, tidak visual QR) --}}
                    @if ($peminjaman->status === 'disetujui' && $peminjaman->qr_code && in_array(auth()->user()->role, ['admin', 'petugas']))
                        <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Kode QR Peminjaman:</p>
                                    <p class="font-mono text-lg font-bold text-gray-800">{{ $peminjaman->qr_code }}</p>
                                </div>
                                
                                

                            </div>
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

                    {{-- Signature Section (Print Only) --}}
                    <div class="hidden print:flex mt-12 justify-between text-center break-inside-avoid">
                        <div class="w-1/3">
                            <p class="mb-16">Peminjam</p>
                            <p class="font-bold underline">{{ $peminjaman->user->name ?? '......................' }}</p>
                        </div>
                        <div class="w-1/3">
                            <p class="mb-16">Petugas Sarpras</p>
                            <p class="font-bold underline">{{ $peminjaman->petugas->name ?? '......................' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t flex flex-wrap justify-between items-center gap-3 print:hidden">
                        <a href="{{ route('peminjaman.index', request()->query()) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Kembali</a>
                        
                        <div class="flex gap-3">

                            @if (in_array(auth()->user()->role, ['admin', 'petugas']))
                                @if ($peminjaman->status === 'menunggu')
                                    <a href="{{ route('peminjaman.edit', $peminjaman) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
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
