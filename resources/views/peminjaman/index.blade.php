<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Peminjaman') }}
            </h2>
            @if (in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                <a href="{{ route('peminjaman.create') }}" 
                   class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan Peminjaman
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    
                    {{-- FILTER SECTION --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[150px]">
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ ($filters['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="selesai" {{ ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditolak" {{ ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <label for="tanggal_mulai" class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}"
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}"
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Nama Peminjam / Barang..."
                                       oninput="debounceSubmit()">
                            </div>
                            <script>
                                let timeout = null;
                                function debounceSubmit() {
                                    clearTimeout(timeout);
                                    timeout = setTimeout(function () {
                                        const form = document.getElementById('search').closest('form');
                                        form.submit();
                                    }, 600);
                                }
                                document.addEventListener("DOMContentLoaded", function() {
                                    const searchInput = document.getElementById('search');
                                    if(searchInput && searchInput.value) {
                                        searchInput.focus();
                                        const val = searchInput.value;
                                        searchInput.value = '';
                                        searchInput.value = val;
                                    }
                                });
                            </script>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    Filter
                                </button>
                                @if (($filters['status'] ?? '') || ($filters['tanggal_mulai'] ?? '') || ($filters['tanggal_selesai'] ?? '') || request('search'))
                                    <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">
                                        Reset
                                    </a>
                                @endif
                                @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                                    <a href="{{ route('export.peminjaman', request()->query()) }}" 
                                       class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- QR CODE QUICK LOOKUP - Hanya untuk Admin/Petugas --}}
                    @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-6 border border-blue-200">
                        <form method="POST" action="{{ route('pengembalian.lookup-qr') }}" id="qrLookupForm" class="flex flex-wrap items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <label for="qr_code" class="block text-xs font-medium text-gray-700 mb-1">
                                    📱 Cari Peminjaman dengan Kode QR
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="qr_code" id="qr_code" 
                                           class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Masukkan atau scan kode QR peminjaman..." autofocus>
                                    <button type="button" id="btnOpenScanner" onclick="openQrScanner()" 
                                            class="px-3 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md"
                                            title="Scan QR dengan Kamera">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9V6a3 3 0 013-3h3M3 15v3a3 3 0 003 3h3M15 3h3a3 3 0 013 3v3M15 21h3a3 3 0 003-3v-3"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h2v2H7zM15 7h2v2h-2zM7 15h2v2H7zM12 12h0"/>
                                        </svg>
                                        <span class="hidden sm:inline">Scan QR</span>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari & Proses Pengembalian
                            </button>
                        </form>
                    </div>

                    {{-- MODAL QR SCANNER (Tanpa Library External) --}}
                    <div id="qrScannerModal" class="fixed inset-0 z-50 hidden">
                        {{-- Backdrop --}}
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeQrScanner()"></div>
                        
                        {{-- Modal Content --}}
                        <div class="fixed inset-0 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden transform transition-all">
                                {{-- Modal Header --}}
                                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-white/20 p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9V6a3 3 0 013-3h3M3 15v3a3 3 0 003 3h3M15 3h3a3 3 0 013 3v3M15 21h3a3 3 0 003-3v-3"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-white font-bold text-lg">Scan QR Code</h3>
                                            <p class="text-purple-100 text-xs">Arahkan kamera ke QR Code peminjaman</p>
                                        </div>
                                    </div>
                                    <button onclick="closeQrScanner()" class="text-white/80 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Scanner Area --}}
                                <div class="p-6">
                                    <div class="relative rounded-xl overflow-hidden border-2 border-gray-200 bg-black" style="aspect-ratio: 1/1;">
                                        <video id="qr-video" class="w-full h-full object-cover" playsinline autoplay muted></video>
                                        {{-- Scan Overlay --}}
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-56 h-56 relative">
                                                {{-- Corner markers --}}
                                                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-lg"></div>
                                                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-lg"></div>
                                                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-lg"></div>
                                                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-lg"></div>
                                                {{-- Scanning line animation --}}
                                                <div class="absolute left-2 right-2 h-0.5 bg-gradient-to-r from-transparent via-red-500 to-transparent animate-scan-line"></div>
                                            </div>
                                        </div>
                                        {{-- Semi-transparent overlay outside scan area --}}
                                        <div class="absolute inset-0 pointer-events-none" style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.4);"></div>
                                    </div>
                                    
                                    {{-- Hidden canvas for frame capture --}}
                                    <canvas id="qr-canvas" class="hidden"></canvas>

                                    {{-- Status Area --}}
                                    <div id="qr-reader-status" class="mt-4 text-center">
                                        <div class="flex items-center justify-center gap-2 text-gray-500 text-sm">
                                            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Menunggu kamera aktif...</span>
                                        </div>
                                    </div>

                                    {{-- Success Indicator (hidden by default) --}}
                                    <div id="qr-scan-success" class="mt-4 hidden">
                                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                                            <div class="bg-green-500 rounded-full p-2 flex-shrink-0">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-green-800 font-semibold text-sm">QR Code Terdeteksi!</p>
                                                <p id="qr-scan-result" class="text-green-600 font-mono text-xs mt-0.5"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t">
                                    <p class="text-xs text-gray-400">Pastikan pencahayaan cukup</p>
                                    <button onclick="closeQrScanner()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Scan Line Animation CSS --}}
                    <style>
                        @keyframes scanLine {
                            0% { top: 0.5rem; }
                            50% { top: calc(100% - 0.5rem); }
                            100% { top: 0.5rem; }
                        }
                        .animate-scan-line {
                            animation: scanLine 2.5s ease-in-out infinite;
                        }
                    </style>

                    {{-- QR Scanner - Native API (Tanpa Library) --}}
                    <script>
                        let videoStream = null;
                        let scanInterval = null;
                        let scannerActive = false;

                        function openQrScanner() {
                            const modal = document.getElementById('qrScannerModal');
                            modal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';

                            // Reset status
                            document.getElementById('qr-scan-success').classList.add('hidden');
                            document.getElementById('qr-reader-status').classList.remove('hidden');
                            document.getElementById('qr-reader-status').innerHTML = `
                                <div class="flex items-center justify-center gap-2 text-gray-500 text-sm">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Menunggu kamera aktif...</span>
                                </div>
                            `;

                            startNativeScanner();
                        }

                        function closeQrScanner() {
                            const modal = document.getElementById('qrScannerModal');
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                            stopNativeScanner();
                        }

                        async function startNativeScanner() {
                            if (scannerActive) return;

                            const video = document.getElementById('qr-video');
                            const canvas = document.getElementById('qr-canvas');
                            const ctx = canvas.getContext('2d', { willReadFrequently: true });

                            // Check if BarcodeDetector is supported (Chrome Android ✅)
                            const hasBarcodeDetector = ('BarcodeDetector' in window);
                            let barcodeDetector = null;

                            if (hasBarcodeDetector) {
                                try {
                                    barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
                                } catch(e) {
                                    console.warn('BarcodeDetector init failed:', e);
                                }
                            }

                            if (!barcodeDetector) {
                                // Fallback: show error with instructions
                                document.getElementById('qr-reader-status').innerHTML = `
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                                        <p class="text-yellow-800 text-sm font-medium">⚠️ Browser tidak mendukung QR scanner</p>
                                        <p class="text-yellow-600 text-xs mt-1">Gunakan <strong>Chrome</strong> di HP Android untuk fitur scan QR.</p>
                                        <p class="text-yellow-600 text-xs mt-1">Atau ketik kode QR secara manual di kolom input.</p>
                                    </div>
                                `;
                                return;
                            }

                            try {
                                // Request camera — prefer back camera for phone
                                videoStream = await navigator.mediaDevices.getUserMedia({
                                    video: {
                                        facingMode: { ideal: 'environment' },
                                        width: { ideal: 640 },
                                        height: { ideal: 640 }
                                    },
                                    audio: false
                                });

                                video.srcObject = videoStream;
                                await video.play();
                                scannerActive = true;

                                // Update status
                                document.getElementById('qr-reader-status').innerHTML = `
                                    <div class="flex items-center justify-center gap-2 text-blue-600 text-sm">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Kamera aktif — Arahkan ke QR Code...</span>
                                    </div>
                                `;

                                // Start scanning loop using BarcodeDetector
                                scanInterval = setInterval(async () => {
                                    if (!scannerActive || video.readyState !== video.HAVE_ENOUGH_DATA) return;

                                    try {
                                        const barcodes = await barcodeDetector.detect(video);
                                        if (barcodes.length > 0) {
                                            const qrValue = barcodes[0].rawValue;
                                            if (qrValue) {
                                                onQrDetected(qrValue);
                                            }
                                        }
                                    } catch(e) {
                                        // Detection error, just continue scanning
                                    }
                                }, 250); // Scan every 250ms

                            } catch (err) {
                                console.error('Camera access error:', err);
                                let errorMsg = 'Gagal mengakses kamera.';
                                if (err.name === 'NotAllowedError') {
                                    errorMsg = 'Izin kamera ditolak. Izinkan akses kamera di pengaturan browser.';
                                } else if (err.name === 'NotFoundError') {
                                    errorMsg = 'Tidak ada kamera yang ditemukan di perangkat ini.';
                                } else if (err.name === 'NotReadableError') {
                                    errorMsg = 'Kamera sedang digunakan oleh aplikasi lain.';
                                }
                                
                                document.getElementById('qr-reader-status').innerHTML = `
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                                        <p class="text-red-700 text-sm font-medium">❌ ${errorMsg}</p>
                                        <p class="text-red-500 text-xs mt-1">Pastikan Anda mengakses via HTTPS (ngrok) dan izinkan akses kamera.</p>
                                    </div>
                                `;
                            }
                        }

                        function stopNativeScanner() {
                            scannerActive = false;

                            if (scanInterval) {
                                clearInterval(scanInterval);
                                scanInterval = null;
                            }

                            if (videoStream) {
                                videoStream.getTracks().forEach(track => track.stop());
                                videoStream = null;
                            }

                            const video = document.getElementById('qr-video');
                            if (video) {
                                video.srcObject = null;
                            }
                        }

                        function onQrDetected(decodedText) {
                            // Prevent double scanning
                            if (!scannerActive) return;
                            scannerActive = false;

                            // Vibrate if supported (nice feedback on phone)
                            if (navigator.vibrate) navigator.vibrate(200);

                            // Show success
                            document.getElementById('qr-reader-status').classList.add('hidden');
                            document.getElementById('qr-scan-success').classList.remove('hidden');
                            document.getElementById('qr-scan-result').textContent = decodedText;

                            // Fill the input
                            document.getElementById('qr_code').value = decodedText;

                            // Stop scanner
                            stopNativeScanner();

                            // Auto-submit after short delay so user sees the success feedback
                            setTimeout(() => {
                                closeQrScanner();
                                document.getElementById('qrLookupForm').submit();
                            }, 800);
                        }

                        // Close on Escape key
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape') {
                                closeQrScanner();
                            }
                        });
                    </script>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg p-4 text-white">
                            <p class="text-yellow-100 text-sm">Menunggu</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'menunggu')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <p class="text-green-100 text-sm">Disetujui</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'disetujui')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <p class="text-blue-100 text-sm">Selesai</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'selesai')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                            <p class="text-red-100 text-sm">Ditolak</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'ditolak')->count() }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kembali</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($peminjaman as $pinjam)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">#{{ $pinjam->id }}</td>
                                        @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $pinjam->user->name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $pinjam->user->email ?? '-' }}</div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            @if ($pinjam->details && $pinjam->details->count() > 0)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $pinjam->details->count() }} unit
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @foreach ($pinjam->details->take(2) as $detail)
                                                        @if($detail->sarprasUnit)
    <span class="inline-block bg-gray-100 rounded px-1 mr-1">{{ $detail->sarprasUnit->kode_unit }}</span>
@else
    <span class="inline-block bg-amber-100 text-amber-800 rounded px-1 mr-1">{{ $detail->quantity }}x</span>
@endif
                                                    @endforeach
                                                    @if ($pinjam->details->count() > 2)
                                                        <span class="text-gray-400">+{{ $pinjam->details->count() - 2 }} lagi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_pinjam?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_kembali_rencana?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @switch($pinjam->status)
                                                @case('menunggu')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                                    @if(Str::contains($pinjam->keterangan, '[REQ-EXT]'))
                                                        <div class="mt-1">
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                                🔄 Minta Perpanjangan
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('disetujui')
                                                @case('dipinjam')
                                                    @if($pinjam->isReadyForPickup())
                                                        @if($pinjam->status === 'dipinjam')
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">📦 Ambil Sebagian</span>
                                                        @else
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">📦 Siap Diambil</span>
                                                        @endif
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sedang Dipinjam</span>
                                                    @endif

                                                    @if ($pinjam->tgl_kembali_rencana && $pinjam->isOngoing())
                                                        @php
                                                            $daysLeft = now()->diffInDays($pinjam->tgl_kembali_rencana, false);
                                                        @endphp
                                                        
                                                        <div class="mt-1">
                                                            @if ($daysLeft < 0)
                                                                {{-- Terlambat --}}
                                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-600 text-white">
                                                                    ⚠️ Telat {{ abs(intval($daysLeft)) }} Hari
                                                                </span>
                                                            @elseif ($daysLeft < 1)
                                                                {{-- Hari Ini Terakhir --}}
                                                                @if(now()->isSameDay($pinjam->tgl_kembali_rencana))
                                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-orange-500 text-white">
                                                                        📅 Hari Ini
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-500 text-white">
                                                                        ⏳ Besok
                                                                    </span>
                                                                @endif
                                                            @elseif ($daysLeft <= 3)
                                                                {{-- Kurang dari 3 hari (Warning) --}}
                                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-400 text-yellow-900">
                                                                    ⏳ Sisa {{ intval($daysLeft) }} Hari
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('selesai')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Selesai</span>
                                                    @break
                                                @case('ditolak')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                                    {{-- TOMBOL AKSI UTAMA BERDASARKAN STATUS --}}
                                                    @if ($pinjam->status === 'menunggu')
                                                        <a href="{{ route('peminjaman.edit', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded hover:bg-yellow-600 transition shadow-sm" title="Verifikasi Pengajuan">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            Verifikasi
                                                        </a>
                                                    @elseif ($pinjam->status === 'disetujui' || $pinjam->status === 'dipinjam')
                                                        @if($pinjam->isReadyForPickup())
                                                            <a href="{{ route('peminjaman.handover', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-xs font-bold rounded hover:bg-amber-700 transition shadow-sm" title="Serah Terima Barang">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                                Serah Terima
                                                            </a>
                                                        @else
                                                            <a href="{{ route('pengembalian.create', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition shadow-sm" title="Proses Pengembalian">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                Kembalikan
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif

                                                {{-- TOMBOL DETAIL (SELALU ADA) --}}
                                                <a href="{{ route('peminjaman.show', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-2 py-1.5 {{ (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) && in_array($pinjam->status, ['menunggu', 'disetujui'])) ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }} text-xs font-medium rounded transition" title="Lihat Detail">
                                                    @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) && in_array($pinjam->status, ['menunggu', 'disetujui']))
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    @else
                                                        Detail
                                                    @endif
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ !in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) ? 7 : 6 }}" class="px-6 py-12 text-center text-gray-500">
                                            Belum ada data peminjaman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($peminjaman->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $peminjaman->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
