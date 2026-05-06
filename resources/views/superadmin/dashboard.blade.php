@extends('layouts.app')
@section('title', 'Dashboard Super Admin')
@section('content')

    {{-- TOOLBAR FILTER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="btn-group me-2 mb-2" role="group">
            <a href="?start={{ now()->toDateString() }}&end={{ now()->toDateString() }}"
                class="btn btn-outline-primary {{ request('start') == now()->toDateString() ? 'active' : '' }}">Hari Ini</a>
            <a href="?start={{ now()->startOfWeek()->toDateString() }}&end={{ now()->endOfWeek()->toDateString() }}"
                class="btn btn-outline-primary {{ request('start') == now()->startOfWeek()->toDateString() ? 'active' : '' }}">Minggu
                Ini</a>
            <a href="?start={{ now()->startOfMonth()->toDateString() }}&end={{ now()->endOfMonth()->toDateString() }}"
                class="btn btn-outline-primary {{ request('start') == now()->startOfMonth()->toDateString() ? 'active' : '' }}">Bulan
                Ini</a>
        </div>
        <form class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <input type="date" name="start" value="{{ $start }}" class="form-control form-control-sm" style="width:auto;">
            <span class="text-muted">s/d</span>
            <input type="date" name="end" value="{{ $end }}" class="form-control form-control-sm" style="width:auto;">
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-sm btn-secondary"><i
                    class="bi bi-arrow-clockwise"></i> Reset</a>
        </form>
    </div>

    {{-- BARIS 1: STATISTIK UTAMA --}}
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-car-front fs-2 text-primary"></i>
                <h6 class="text-muted mt-2">Total Kendaraan</h6>
                <h2 class="fw-bold">{{ $totalKendaraan }}</h2>
                <small class="text-muted">Terdaftar</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-p-circle fs-2 text-success"></i>
                <h6 class="text-muted mt-2">Parkir Aktif</h6>
                <h2 class="fw-bold">{{ $parkirAktif }}</h2>
                <small class="text-muted">sedang parkir</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-down-circle fs-2 text-warning"></i>
                <h6 class="text-muted mt-2">Check-in Hari Ini</h6>
                <h2 class="fw-bold">{{ $checkinHariIni }}</h2>
                <small class="text-muted">{{ $start }} – {{ $end }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-up-circle fs-2 text-danger"></i>
                <h6 class="text-muted mt-2">Check-out Hari Ini</h6>
                <h2 class="fw-bold">{{ $checkoutHariIni }}</h2>
                <small class="text-muted">periode yang sama</small>
            </div>
        </div>
    </div>

    {{-- BARIS STATISTIK ROLE --}}
    <div class="row g-3 mt-2">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h6 class="text-muted">Super Admin</h6>
                <h3 class="fw-bold">{{ $totalSuperadmin }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h6 class="text-muted">Admin</h6>
                <h3 class="fw-bold">{{ $totalAdmin }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h6 class="text-muted">Petugas</h6>
                <h3 class="fw-bold">{{ $totalPetugas }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h6 class="text-muted">Mahasiswa</h6>
                <h3 class="fw-bold">{{ $totalMahasiswa }}</h3>
            </div>
        </div>
    </div>

    {{-- GRAFIK UTAMA & DONUT --}}
    <div class="row g-3 mt-2">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="mb-0">Aktivitas Parkir 7 Hari Terakhir</h5>
                <canvas id="parkirChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <h5>Parkir Aktif per Jenis</h5>
                <canvas id="jenisChart" height="200"></canvas>
                <div class="text-center mt-2">
                    <span class="badge bg-primary">Mobil: {{ $aktifMobil }}</span>
                    <span class="badge bg-success ms-2">Motor: {{ $aktifMotor }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- JAM TERSIBUK & AKTIVITAS TERBARU --}}
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <h5>Jam Tersibuk Hari Ini</h5>
                <div class="text-center my-3">
                    <h1 class="display-4 text-primary">{{ $jamTersibukLabel }}</h1>
                    <p class="lead">{{ $jumlahTersibuk }} check-in</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-3">
                <h5>Aktivitas Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Plat</th>
                                <th>Status</th>
                                <th>Waktu Check-in</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aktivitasTerbaru as $p)
                                <tr>
                                    <td>{{ $p->kendaraan->plat_nomor ?? '-' }}</td>
                                    <td><span
                                            class="badge bg-{{ $p->status == 'active' ? 'warning' : ($p->status == 'completed' ? 'success' : 'danger') }}">{{ $p->status }}</span>
                                    </td>
                                    <td>{{ $p->check_in->format('H:i') }}</td>
                                    <td>{{ $p->check_out ? $p->check_in->diffInMinutes($p->check_out) . ' mnt' : 'berjalan' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada aktivitas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- PARKIR AKTIF TERBARU --}}
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-transparent">
            <h5>Kendaraan Sedang Parkir</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plat</th>
                        <th>Pemilik</th>
                        <th>Check-in</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parkirAktifList as $p)
                        <tr>
                            <td><strong>{{ $p->kendaraan->plat_nomor ?? '-' }}</strong></td>
                            <td>{{ $p->kendaraan->user->name ?? 'N/A' }}</td>
                            <td>{{ $p->check_in->format('H:i') }}</td>
                            <td>{{ now()->diffInMinutes($p->check_in) }} mnt</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada parkir aktif</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        const ctx = document.getElementById('parkirChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Check-in',
                        data: {!! json_encode($dataCheckin) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Check-out',
                        data: {!! json_encode($dataCheckout) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220,53,69,0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const ctx2 = document.getElementById('jenisChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Mobil', 'Motor'],
                datasets: [{
                    data: [{{ $aktifMobil }}, {{ $aktifMotor }}],
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