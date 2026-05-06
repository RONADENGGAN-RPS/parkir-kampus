@extends('layouts.app')
@section('title', 'Laporan Parkir')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Laporan Parkir</h4>
        <div>
            <button class="btn btn-success btn-sm me-2" onclick="alert('Export PDF (coming soon)')"><i
                    class="bi bi-file-earmark-pdf"></i> PDF</button>
            <button class="btn btn-info btn-sm" onclick="alert('Export Excel (coming soon)')"><i
                    class="bi bi-file-earmark-excel"></i> Excel</button>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Dari Tanggal</label>
                    <input type="date" name="start" class="form-control" value="{{ $start }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Sampai Tanggal</label>
                    <input type="date" name="end" class="form-control" value="{{ $end }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2">
                <small class="text-muted">Total Parkir</small>
                <h5 class="fw-bold">{{ $totalParkir }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-warning bg-opacity-10">
                <small class="text-muted">Aktif</small>
                <h5 class="fw-bold text-warning">{{ $totalCheckin }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-success bg-opacity-10">
                <small class="text-muted">Selesai</small>
                <h5 class="fw-bold text-success">{{ $totalCheckout }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-danger bg-opacity-10">
                <small class="text-muted">Pelanggaran</small>
                <h5 class="fw-bold text-danger">{{ $pelanggaran }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-info bg-opacity-10">
                <small class="text-muted">Rata Durasi</small>
                <h5 class="fw-bold">{{ $rataDurasi }} <small class="fs-6">mnt</small></h5>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-2 bg-primary bg-opacity-10">
                <small class="text-muted">Mobil / Motor</small>
                <h5 class="fw-bold">{{ $totalMobil }} / {{ $totalMotor }}</h5>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-3">
                <h5>Tren Parkir Harian</h5>
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-3">
                <h5>Komposisi Jenis Kendaraan</h5>
                <canvas id="jenisChart" height="200"></canvas>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Grafik tren
        const ctx1 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Check-in',
                        data: {!! json_encode($dataCheckin) !!},
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253,126,20,0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Check-out',
                        data: {!! json_encode($dataCheckout) !!},
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25,135,84,0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pelanggaran',
                        data: {!! json_encode($dataViolation) !!},
                        borderColor: '#dc3545',
                        borderDash: [5],
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Grafik donut jenis kendaraan
        const ctx2 = document.getElementById('jenisChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Mobil', 'Motor'],
                datasets: [{
                    data: [{{ $totalMobil }}, {{ $totalMotor }}],
                    backgroundColor: ['#0d6efd', '#198754'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
@endsection