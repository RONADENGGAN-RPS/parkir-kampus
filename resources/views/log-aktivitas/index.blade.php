@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Log Aktivitas</h4>
    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterPanel">
        <i class="bi bi-funnel"></i> Filter
    </button>
</div>

{{-- Panel Filter --}}
<div class="collapse mb-3" id="filterPanel">
    <div class="card border-0 shadow-sm p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="start" class="form-control" value="{{ request('start') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="end" class="form-control" value="{{ request('end') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Modul</label>
                <select name="module" class="form-select">
                    <option value="">Semua</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('log-aktivitas.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Log --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Modul</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="small">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                    <td><span class="badge bg-info">{{ $log->action }}</span></td>
                    <td class="small">{{ Str::limit($log->description, 60) }}</td>
                    <td><code>{{ $log->ip_address }}</code></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada log aktivitas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end p-2">
        {{ $logs->links() }}
    </div>
</div>

@endsection