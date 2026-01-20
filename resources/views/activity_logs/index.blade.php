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
