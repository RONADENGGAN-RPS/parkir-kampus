@extends('layouts.app')
@section('title', 'Dashboard Petugas')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Dashboard Petugas</h4>
        <a href="{{ route('scan') }}" class="btn btn-primary">
            <i class="bi bi-qr-code-scan"></i> Scan QR Sekarang
        </a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-p-circle fs-2 text-success"></i>
                <h6 class="text-muted mt-2">Parkir Aktif</h6>
                <h3 class="fw-bold">{{ $parkirAktif }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-down-circle fs-2 text-warning"></i>
                <h6 class="text-muted mt-2">Check-in Hari Ini</h6>
                <h3 class="fw-bold">{{ $checkinHariIni }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-up-circle fs-2 text-info"></i>
                <h6 class="text-muted mt-2">Check-out Hari Ini</h6>
                <h3 class="fw-bold">{{ $checkoutHariIni }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                <h6 class="text-muted mt-2">Pelanggaran</h6>
                <h3 class="fw-bold">{{ $pelanggaranHariIni }}</h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5><i class="bi bi-clock-history"></i> Kendaraan Sedang Parkir</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plat</th>
                        <th>Pemilik</th>
                        <th>Check-in</th>
                        <th>Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parkirAktifList as $p)
                        <tr>
                            <td><strong>{{ $p->kendaraan->plat_nomor ?? '-' }}</strong></td>
                            <td>{{ $p->kendaraan->user->name ?? 'N/A' }}</td>
                            <td>{{ $p->check_in->format('H:i') }}</td>
                            <td>{{ now()->diffInMinutes($p->check_in) }} menit</td>
                            <td>
                                <a href="{{ route('parkir.show', $p->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada parkir aktif</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Scan Pribadi Petugas --}}
    @if($riwayatSaya->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5><i class="bi bi-person-check"></i> Riwayat Scan Saya</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Plat</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSaya as $p)
                            <tr>
                                <td>{{ $p->kendaraan->plat_nomor ?? '-' }}</td>
                                <td><span
                                        class="badge bg-{{ $p->status == 'active' ? 'warning' : ($p->status == 'completed' ? 'success' : 'danger') }}">{{ $p->status }}</span>
                                </td>
                                <td>{{ $p->check_in->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('parkir.show', $p->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection