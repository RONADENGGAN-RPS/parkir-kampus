@extends('layouts.app')
@section('title', 'Riwayat Parkir')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Parkir</h4>
        <div>
            <button class="btn btn-success btn-sm me-2" onclick="alert('Export PDF (coming soon)')"><i
                    class="bi bi-file-earmark-pdf"></i> PDF</button>
            <button class="btn btn-info btn-sm" onclick="alert('Export Excel (coming soon)')"><i
                    class="bi bi-file-earmark-excel"></i> Excel</button>
        </div>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-light">
                <small class="text-muted">Total</small>
                <h5 class="mb-0">{{ $statistik['total'] }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-warning bg-opacity-10">
                <small class="text-muted">Aktif</small>
                <h5 class="mb-0 text-warning">{{ $statistik['aktif'] }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-success bg-opacity-10">
                <small class="text-muted">Selesai</small>
                <h5 class="mb-0 text-success">{{ $statistik['selesai'] }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-danger bg-opacity-10">
                <small class="text-muted">Pelanggaran</small>
                <h5 class="mb-0 text-danger">{{ $statistik['pelanggaran'] }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-info bg-opacity-10">
                <small class="text-muted">Rata Durasi</small>
                <h5 class="mb-0">{{ $statistik['rata_durasi'] }} <small class="fs-6">mnt</small></h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <button class="btn btn-outline-primary w-100 h-100" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
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
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="violation" {{ request('status') == 'violation' ? 'selected' : '' }}>Pelanggaran
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cari Plat/Merk</label>
                    <input type="text" name="search" class="form-control" placeholder="B 1234"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Terapkan</button>
                    <a href="{{ route('parkir.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="parkirTable">
                <thead class="table-light">
                    <tr>
                        <th>Plat Nomor</th>
                        <th>Pemilik</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parkirs as $p)
                        <tr>
                            <td><strong>{{ $p->kendaraan->plat_nomor ?? '-' }}</strong></td>
                            <td>{{ $p->kendaraan->user->name ?? 'N/A' }}</td>
                            <td>{{ $p->check_in->format('d/m/Y H:i') }}</td>
                            <td>{{ $p->check_out ? $p->check_out->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $p->durasi ? $p->durasi . ' mnt' : '-' }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $p->status == 'active' ? 'warning' : ($p->status == 'completed' ? 'success' : 'danger') }}">
                                    {{ $p->status == 'active' ? 'Aktif' : ($p->status == 'completed' ? 'Selesai' : 'Pelanggaran') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('parkir.show', $p->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data parkir</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-2">
            {{ $parkirs->links() }}
        </div>
    </div>

@endsection