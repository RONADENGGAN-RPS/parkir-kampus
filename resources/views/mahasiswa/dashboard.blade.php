@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')

    <div class="dashboard-wrapper">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-gradient">Selamat Datang,</span> {{ auth()->user()->name }} 👋
                </h4>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar-check me-1"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Statistik Personal --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card border-0 shadow-sm text-center p-3 h-100"
                    style="background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(255,255,255,0.8));">
                    <div class="icon-circle bg-primary bg-opacity-25 mx-auto mb-2">
                        <i class="bi bi-car-front fs-3 text-primary"></i>
                    </div>
                    <h6 class="text-muted small text-uppercase fw-bold">Kendaraan</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $kendaraanCount }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-0 shadow-sm text-center p-3 h-100"
                    style="background: linear-gradient(135deg, rgba(25,135,84,0.1), rgba(255,255,255,0.8));">
                    <div class="icon-circle bg-success bg-opacity-25 mx-auto mb-2">
                        <i class="bi bi-p-circle fs-3 text-success"></i>
                    </div>
                    <h6 class="text-muted small text-uppercase fw-bold">Parkir Aktif</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $parkirAktif }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-0 shadow-sm text-center p-3 h-100"
                    style="background: linear-gradient(135deg, rgba(13,202,240,0.1), rgba(255,255,255,0.8));">
                    <div class="icon-circle bg-info bg-opacity-25 mx-auto mb-2">
                        <i class="bi bi-check-circle fs-3 text-info"></i>
                    </div>
                    <h6 class="text-muted small text-uppercase fw-bold">Selesai</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $parkirSelesai }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-0 shadow-sm text-center p-3 h-100"
                    style="background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,255,255,0.8));">
                    <div class="icon-circle bg-warning bg-opacity-25 mx-auto mb-2">
                        <i class="bi bi-clock-history fs-3 text-warning"></i>
                    </div>
                    <h6 class="text-muted small text-uppercase fw-bold">Rata‑rata Durasi</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $rataDurasi }} <small class="fs-6 text-muted">mnt</small></h3>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Grafik Donat --}}
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Status Parkir</h6>
                        <div class="d-flex justify-content-center">
                            <canvas id="statusChart" width="200" height="200"></canvas>
                        </div>
                        <div class="mt-3 small">
                            <span class="text-success">● Aktif: {{ $parkirAktif }}</span>
                            <span class="ms-3 text-info">● Selesai: {{ $parkirSelesai }}</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Aktivitas Terkini --}}
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i>Aktivitas Terkini</h5>
                        <span class="badge bg-primary rounded-pill">{{ count($riwayatTerbaru) }} aktivitas</span>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @if($riwayatTerbaru->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada aktivitas parkir
                            </div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($riwayatTerbaru as $p)
                                    <li class="list-group-item border-0 px-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($p->status == 'active')
                                                    <span class="badge bg-warning rounded-pill"><i
                                                            class="bi bi-hourglass-split"></i></span>
                                                @elseif($p->status == 'violation')
                                                    <span class="badge bg-danger rounded-pill"><i
                                                            class="bi bi-exclamation-triangle"></i></span>
                                                @else
                                                    <span class="badge bg-success rounded-pill"><i
                                                            class="bi bi-check-circle"></i></span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>{{ $p->kendaraan->plat_nomor ?? 'N/A' }}</strong>
                                                    <small class="text-muted">{{ $p->check_in->format('d/m/Y H:i') }}</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="small text-muted">
                                                        @if($p->status == 'active')
                                                            Sedang parkir
                                                        @elseif($p->status == 'violation')
                                                            Pelanggaran
                                                        @else
                                                            Selesai
                                                        @endif
                                                    </span>
                                                    @if($p->check_out)
                                                        <small class="text-muted">{{ $p->check_in->diffInMinutes($p->check_out) }}
                                                            mnt</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <a href="{{ route('mahasiswa.parkir') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            Lihat Semua Riwayat <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('statusChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Selesai'],
                    datasets: [{
                        data: [{{ $parkirAktif }}, {{ $parkirSelesai }}],
                        backgroundColor: ['#10b981', '#6366f1'],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '70%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>
@endpush