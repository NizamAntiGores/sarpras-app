<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola User</h2>
            <a href="{{ route('users.create') }}"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Filter & Search --}}
                    <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="flex gap-2">
                            <a href="{{ route('users.index') }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-full {{ !request('role') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Semua
                            </a>
                            <a href="{{ route('users.index', ['role' => 'admin'] + request()->except(['role', 'page'])) }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-full {{ request('role') == 'admin' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Admin
                            </a>
                            <a href="{{ route('users.index', ['role' => 'petugas'] + request()->except(['role', 'page'])) }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-full {{ request('role') == 'petugas' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Petugas
                            </a>
                            <a href="{{ route('users.index', ['role' => 'guru'] + request()->except(['role', 'page'])) }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-full {{ request('role') == 'guru' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Guru
                            </a>
                            <a href="{{ route('users.index', ['role' => 'siswa'] + request()->except(['role', 'page'])) }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-full {{ request('role') == 'siswa' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Siswa
                            </a>
                        </div>
                        <div class="w-full md:w-1/3">
                            <form action="{{ route('users.index') }}" method="GET">
                                @if(request('role')) <input type="hidden" name="role" value="{{ request('role') }}">
                                @endif
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Cari nama, email, atau NIP..." oninput="debounceSubmit()">
                                </div>
                            </form>
                        </div>
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

                        document.addEventListener("DOMContentLoaded", function () {
                            const searchInput = document.getElementById('search');
                            if (searchInput && searchInput.value) {
                                searchInput.focus();
                                const val = searchInput.value;
                                searchInput.value = '';
                                searchInput.value = val;
                            }
                        });
                    </script>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Identitas (NIS/NIP)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            @if($user->kelas)
                                                <div class="text-xs text-gray-500">Kelas: {{ $user->kelas }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                            @if($user->role == 'siswa')
                                                <span class="text-xs text-gray-400 block">NIS</span>
                                            @elseif($user->role == 'guru')
                                                <span class="text-xs text-gray-400 block">NIP</span>
                                            @endif
                                            {{ $user->nomor_induk ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : '' }}
                                                                {{ $user->role === 'petugas' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $user->role === 'peminjam' ? 'bg-gray-100 text-gray-800' : '' }}
                                                                {{ $user->role === 'siswa' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $user->role === 'guru' ? 'bg-purple-100 text-purple-800' : '' }}
                                                            ">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->kontak ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="text-blue-600 hover:text-blue-900" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                @if ($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                        class="inline" onsubmit="return confirm('Yakin hapus user ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            title="Hapus">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data user
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($users->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $users->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>