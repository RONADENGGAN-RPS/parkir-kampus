@extends('layouts.app')
@section('title', 'Riwayat Parkir Saya')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-gradient"><i class="bi bi-clock-history me-2"></i>Riwayat Parkir</h4>
            <p class="text-muted mb-0">Pantau semua aktivitas parkir kendaraan Anda.</p>
        </div>
        <span class="badge bg-primary bg-gradient fs-6 px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-collection me-1"></i> {{ $total }} Riwayat
        </span>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 bg-primary bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-list-ol fs-2 text-primary"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Total Parkir</h6>
                <h3 class="fw-bold text-primary">{{ $total }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 bg-warning bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-p-circle fs-2 text-warning"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Sedang Parkir</h6>
                <h3 class="fw-bold text-warning">{{ $aktif }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 bg-success bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-check-circle fs-2 text-success"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Selesai</h6>
                <h3 class="fw-bold text-success">{{ $selesai }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 bg-danger bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Pelanggaran</h6>
                <h3 class="fw-bold text-danger">{{ $pelanggaran }}</h3>
            </div>
        </div>
    </div>

    {{-- Filter Tanggal --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body d-md-flex align-items-end gap-3">
            <div class="flex-grow-1">
                <label class="form-label small fw-bold text-secondary">📅 Rentang Tanggal</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="date" name="start" class="form-control rounded-pill" value="{{ request('start') }}"
                            placeholder="Dari">
                    </div>
                    <div class="col-6">
                        <input type="date" name="end" class="form-control rounded-pill" value="{{ request('end') }}"
                            placeholder="Sampai">
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-funnel me-1"></i>
                    Filter</button>
                <a href="{{ route('mahasiswa.parkir') }}" class="btn btn-outline-secondary rounded-pill px-4"><i
                        class="bi bi-x-circle me-1"></i> Reset</a>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark bg-gradient">
                    <tr>
                        <th class="ps-4"><i class="bi bi-car-front me-1"></i>Plat Nomor</th>
                        <th>Status</th>
                        <th>Check‑in</th>
                        <th>Check‑out</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parkirs as $p)
                        <tr class="align-middle">
                            <td class="ps-4">
                                <span class="fw-bold">{{ $p->kendaraan->plat_nomor ?? '-' }}</span>
                            </td>
                            <td>
                                @if($p->status == 'active')
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i> Sedang Parkir
                                    </span>
                                @elseif($p->status == 'completed')
                                    <span class="badge bg-success rounded-pill px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif($p->status == 'violation')
                                    <span class="badge bg-danger rounded-pill px-2 py-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Pelanggaran
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-2 py-1">{{ $p->status }}</span>
                                @endif
                            </td>
                            <td>{{ $p->check_in ? $p->check_in->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $p->check_out ? $p->check_out->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($p->durasi)
                                    @if($p->durasi >= 60)
                                        <span class="fw-bold">{{ floor($p->durasi / 60) }}</span> jam {{ $p->durasi % 60 }} mnt
                                    @else
                                        <span class="fw-bold">{{ $p->durasi }}</span> mnt
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada riwayat parkir
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($parkirs->hasPages())
            <div class="card-footer bg-light d-flex justify-content-center py-2">
                {{ $parkirs->links() }}
            </div>
        @endif
    </div>

@endsection

@push('styles')
    <style>
        .text-gradient {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
        }

        .table-dark.bg-gradient {
            background: linear-gradient(135deg, #212529, #343a40) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }

        .rounded-pill.badge {
            font-size: 0.8rem;
        }
    </style>
@endpush