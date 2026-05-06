@extends('layouts.app')
@section('title', 'Laporan Saya')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-gradient"><i class="bi bi-bar-chart-line me-2"></i>Laporan Pribadi</h4>
            <p class="text-muted mb-0">Ringkasan aktivitas parkir Anda dalam periode terpilih.</p>
        </div>
        <span class="badge bg-primary bg-gradient fs-6 px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-collection me-1"></i> {{ $totalParkir }} Parkir
        </span>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm text-center p-3 bg-primary bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-bar-chart fs-2 text-primary"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Total Parkir</h6>
                <h3 class="fw-bold text-primary mb-0">{{ $totalParkir }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm text-center p-3 bg-danger bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Pelanggaran</h6>
                <h3 class="fw-bold text-danger mb-0">{{ $totalViolation }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm text-center p-3 bg-success bg-opacity-10 rounded-4 stat-card">
                <i class="bi bi-graph-up fs-2 text-success"></i>
                <h6 class="text-muted mt-2 small text-uppercase fw-bold">Rata‑rata / Hari</h6>
                <h3 class="fw-bold text-success mb-0">
                    {{ $totalParkir > 0 ? round($totalParkir / count($labels), 1) : 0 }}
                </h3>
            </div>
        </div>
    </div>

    {{-- Filter Tanggal --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body d-md-flex align-items-end gap-3">
            <div class="flex-grow-1">
                <label class="form-label small fw-bold text-secondary">📅 Periode Laporan</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="date" name="start" class="form-control rounded-pill" value="{{ $start }}"
                            placeholder="Dari">
                    </div>
                    <div class="col-6">
                        <input type="date" name="end" class="form-control rounded-pill" value="{{ $end }}"
                            placeholder="Sampai">
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-funnel me-1"></i>
                    Filter</button>
                <a href="{{ route('mahasiswa.laporan') }}" class="btn btn-outline-secondary rounded-pill px-4"><i
                        class="bi bi-x-circle me-1"></i> Reset</a>
            </div>
        </div>
    </div>

    {{-- Grafik Utama --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Parkir Harian</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="downloadChart()"><i class="bi bi-file-image me-1"></i>
                            Simpan Gambar</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <canvas id="myParkirChart" height="100"></canvas>
        </div>
    </div>

    {{-- Ringkasan Tambahan --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                <p class="small text-muted mb-1"><i class="bi bi-info-circle me-1"></i> Total Parkir</p>
                <h4 class="fw-bold text-primary">{{ $totalParkir }} kali</h4>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                <p class="small text-muted mb-1"><i class="bi bi-flag me-1"></i> Pelanggaran</p>
                <h4 class="fw-bold text-danger">{{ $totalViolation }} kali</h4>
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

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('myParkirChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(13, 110, 253, 0.8)');
            gradient.addColorStop(1, 'rgba(13, 110, 253, 0.2)');

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Jumlah Parkir',
                        data: @json($dataParkir),
                        backgroundColor: gradient,
                        borderColor: '#0d6efd',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: '#0d6efd',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#212529',
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false,
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart',
                    }
                }
            });

            window.downloadChart = function () {
                const link = document.createElement('a');
                link.download = 'laporan-parkir.png';
                link.href = chart.toBase64Image();
                link.click();
            };
        });
    </script>
@endpush