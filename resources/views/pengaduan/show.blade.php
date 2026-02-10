<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengaduan') }} #{{ $pengaduan->id }}
            </h2>
            <a href="{{ route('pengaduan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: DETAIL LAPORAN --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        {{-- Status Header --}}
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-xs font-bold uppercase text-gray-500 tracking-wider">Status Saat Ini</span>
                            @php
                                $statusColors = [
                                    'belum_ditindaklanjuti' => 'bg-red-100 text-red-800',
                                    'sedang_diproses' => 'bg-yellow-100 text-yellow-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'ditutup' => 'bg-gray-100 text-gray-800',
                                    'ditolak' => 'bg-gray-100 text-gray-800', // fallback if manually set
                                ];
                                $statusLabel = [
                                    'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti',
                                    'sedang_diproses' => 'Sedang Diproses',
                                    'selesai' => 'Selesai',
                                    'ditutup' => 'Ditutup',
                                    'ditolak' => 'Ditolak',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$pengaduan->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabel[$pengaduan->status] ?? ucfirst(str_replace('_', ' ', $pengaduan->status)) }}
                            </span>
                        </div>

                        <div class="p-6">
                            {{-- Foto Bukti --}}
                            @if($pengaduan->foto)
                                <div class="mb-6 rounded-lg overflow-hidden border border-gray-200">
                                    <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Foto Bukti" class="w-full h-auto object-cover">
                                </div>
                            @endif

                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $pengaduan->judul }}</h3>
                            
                            <div class="prose prose-sm text-gray-600 mb-6">
                                <p>{{ $pengaduan->deskripsi }}</p>
                            </div>

                            {{-- Metadata Grid --}}
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Pelapor</p>
                                        <p class="text-sm text-gray-500">{{ $pengaduan->user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $pengaduan->user->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Lokasi</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $pengaduan->lokasi->nama_lokasi ?? $pengaduan->lokasi_lainnya ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Barang / Asset</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $pengaduan->sarpras->nama_barang ?? $pengaduan->barang_lainnya ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Waktu Lapor</p>
                                        <p class="text-sm text-gray-500">{{ $pengaduan->created_at->format('d F Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: TIMELINE --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col h-[600px]"> {{-- Fixed height for scroll --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="font-bold text-gray-800">Timeline Penanganan</h3>
                        </div>
                        
                        {{-- Chat Area --}}
                        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50" id="message-container">
                            {{-- Initial Report Bubble --}}
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                        {{ substr($pengaduan->user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-sm text-gray-900">{{ $pengaduan->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $pengaduan->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg rounded-tl-none shadow-sm text-sm text-gray-700 border border-gray-200">
                                        <p class="font-semibold text-gray-900 mb-1">Melaporkan keluhan:</p>
                                        {{ $pengaduan->deskripsi }}
                                    </div>
                                </div>
                            </div>

                            {{-- Responses --}}
                            @foreach($pengaduan->responses as $response)
                                <div class="flex items-start gap-4 {{ $response->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full {{ $response->user->role == 'admin' || $response->user->role == 'petugas' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold">
                                            {{ substr($response->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 {{ $response->user_id === auth()->id() ? 'text-right' : '' }}">
                                        <div class="flex items-center gap-2 mb-1 {{ $response->user_id === auth()->id() ? 'justify-end' : '' }}">
                                            <span class="font-bold text-sm text-gray-900">{{ $response->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $response->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="inline-block {{ $response->user_id === auth()->id() ? 'bg-indigo-50 border border-indigo-100 rounded-tr-none' : 'bg-white border border-gray-200 rounded-tl-none' }} p-4 rounded-lg shadow-sm text-sm text-gray-700 text-left w-full">
                                            @if($response->status)
                                                <div class="mb-2">
                                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-200 text-gray-700">
                                                       Status: {{ $statusLabel[$response->status] ?? ucfirst($response->status) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <p>{{ $response->response }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- System Message if Closed --}}
                            @if(in_array($pengaduan->status, ['selesai', 'ditutup', 'ditolak']))
                                <div class="flex justify-center my-4">
                                    <span class="px-4 py-2 bg-gray-200 rounded-full text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Laporan Ditutup
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Input Area --}}
                        <div class="bg-white p-4 border-t border-gray-200">
                            @if(in_array($pengaduan->status, ['selesai', 'ditutup', 'ditolak']))
                                <div class="text-center py-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <h4 class="text-sm font-bold text-gray-900">Laporan Selesai</h4>
                                    <p class="text-xs text-gray-500 mt-1">Tidak dapat menambahkan respon baru.</p>
                                </div>
                            @else
                                <form action="{{ route('pengaduan.update', $pengaduan) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="flex flex-col gap-4">
                                        {{-- Message Input --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan / Tindakan:</label>
                                            <textarea name="response" rows="3" required minlength="5"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3"
                                                placeholder="Tuliskan catatan tindakan yang dilakukan..."></textarea>
                                            <p class="text-xs text-gray-500 mt-1">*Catatan wajib diisi untuk setiap perubahan status.</p>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex flex-wrap gap-3 pt-2">
                                            {{-- Chat / Reply Button (Available for All) --}}
                                            <button type="submit" name="status" value="chat" 
                                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition transform hover:scale-105 flex justify-center items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                Kirim Pesan
                                            </button>

                                            {{-- Status Actions (Admin/Petugas Only) --}}
                                            @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                                                @if($pengaduan->status == 'belum_ditindaklanjuti')
                                                    <button type="submit" name="status" value="sedang_diproses" 
                                                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow transition transform hover:scale-105 flex justify-center items-center gap-2"
                                                        title="Tandai laporan sedang diproses">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Proses
                                                    </button>
                                                @endif

                                                @if(in_array($pengaduan->status, ['belum_ditindaklanjuti', 'sedang_diproses']))
                                                    <button type="submit" name="status" value="selesai" 
                                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition transform hover:scale-105 flex justify-center items-center gap-2"
                                                        onclick="return confirm('Apakah Anda yakin laporan ini telah selesai ditangani?')"
                                                        title="Tandai laporan selesai & diperbaiki">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Selesai
                                                    </button>
                                                @endif

                                                <button type="submit" name="status" value="ditutup" 
                                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow transition transform hover:scale-105 flex justify-center items-center gap-2"
                                                    onclick="return confirm('Tolak atau Tutup laporan ini?')"
                                                    title="Tolak atau Tutup laporan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Tolak
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto scroll to bottom of chat
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('message-container');
            if(container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-app-layout>
