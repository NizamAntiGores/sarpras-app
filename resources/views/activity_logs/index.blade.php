<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Filter Section --}}
                    <div class="mb-6">
                        <form method="GET" class="flex flex-wrap gap-3 items-end">
                            {{-- Search --}}
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Cari deskripsi atau nama user..."
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            {{-- Action Filter --}}
                            <div class="min-w-[120px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Aksi</label>
                                <select name="action" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Aksi</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- User Filter --}}
                            <div class="min-w-[150px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                                <select name="user_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Date From --}}
                            <div class="min-w-[140px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            {{-- Date To --}}
                            <div class="min-w-[140px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            {{-- Buttons --}}
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Filter
                                </button>
                                @if(request()->hasAny(['search', 'action', 'user_id', 'date_from', 'date_to']))
                                    <a href="{{ route('activity_logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $log->created_at->format('d M Y H:i:s') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            <div class="font-medium">{{ $log->user->name ?? 'System/Guest' }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->user->role ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-2">
                                            @php
                                                $colors = [
                                                    'login' => 'blue',
                                                    'logout' => 'gray',
                                                    'create' => 'green',
                                                    'update' => 'yellow',
                                                    'delete' => 'red',
                                                    'approve' => 'teal',
                                                    'reject' => 'pink',
                                                ];
                                                $color = $colors[$log->action] ?? 'gray';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800 uppercase">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $log->description }}
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-500">
                                            {{ $log->ip_address }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada aktivitas yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logs->hasPages())
                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
