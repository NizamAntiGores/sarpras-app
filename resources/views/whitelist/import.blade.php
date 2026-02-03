<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Import Data Siswa/Guru
            </h2>
            <a href="{{ route('whitelist.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition ease-in-out duration-150">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Info Box --}}
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Cara Import Data</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ol class="list-decimal list-inside space-y-1">
                                        <li>Download template CSV sesuai role (Siswa/Guru)</li>
                                        <li>Isi data sesuai format template</li>
                                        <li>Upload file CSV yang sudah diisi</li>
                                        <li>Data yang sudah ada akan dilewati (tidak duplikat)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Download Template --}}
                    <div class="mb-6 flex gap-3">
                        <a href="{{ route('whitelist.template', ['role' => 'siswa']) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Template Siswa
                        </a>
                        <a href="{{ route('whitelist.template', ['role' => 'guru']) }}"
                            class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Template Guru
                        </a>
                    </div>

                    {{-- Form Upload --}}
                    <form method="POST" action="{{ route('whitelist.import.process') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Role Selection --}}
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Data <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="siswa" class="form-radio text-blue-600" checked>
                                    <span class="ml-2">Siswa (NISN, Nama, Kelas)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="guru" class="form-radio text-purple-600">
                                    <span class="ml-2">Guru (NIP, Nama)</span>
                                </label>
                            </div>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                File CSV <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload file CSV</span>
                                            <input id="file" name="file" type="file" class="sr-only" accept=".csv,.txt">
                                        </label>
                                        <p class="pl-1">atau drag & drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV hingga 2MB</p>
                                </div>
                            </div>
                            <p id="file-name" class="mt-2 text-sm text-gray-600 hidden"></p>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Format Info --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Format CSV</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-blue-700">Siswa:</h4>
                            <code class="block bg-gray-100 p-3 rounded text-sm mt-2">
                                NISN,Nama,Kelas<br>
                                0012345678,Ahmad Rizky,XII RPL 1<br>
                                0012345679,Sarah Putri,XII RPL 2
                            </code>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-purple-700">Guru:</h4>
                            <code class="block bg-gray-100 p-3 rounded text-sm mt-2">
                                NIP,Nama<br>
                                198001012005011001,Pak Joko<br>
                                198502022010012002,Bu Siti
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameEl = document.getElementById('file-name');
            if (fileName) {
                fileNameEl.textContent = '📄 ' + fileName;
                fileNameEl.classList.remove('hidden');
            } else {
                fileNameEl.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
