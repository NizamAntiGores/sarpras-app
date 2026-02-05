@extends('exports.layout')

@section('content')
    @php $title = 'Log Aktivitas Sistem'; @endphp

    <div class="summary-box">
        <h3>Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $logs->count() }}</div>
                <div class="label">Total Log</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $logs->where('action', 'login')->count() }}</div>
                <div class="label">Login</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $logs->where('action', 'create')->count() }}</div>
                <div class="label">Create</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $logs->where('action', 'update')->count() }}</div>
                <div class="label">Update</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $logs->where('action', 'delete')->count() }}</div>
                <div class="label">Delete</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 120px;">Waktu</th>
                <th style="width: 100px;">User</th>
                <th style="width: 70px;">Aksi</th>
                <th>Deskripsi</th>
                <th style="width: 100px;">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>
                        <span class="badge 
                                    @if($log->action == 'login') badge-info
                                    @elseif($log->action == 'create') badge-success
                                    @elseif($log->action == 'update') badge-warning
                                    @elseif($log->action == 'delete') badge-danger
                                    @else badge-secondary
                                    @endif">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($log->description, 80) }}</td>
                    <td class="mono">{{ $log->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection