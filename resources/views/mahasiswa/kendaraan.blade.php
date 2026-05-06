@extends('layouts.app')
@section('title', 'Kendaraan Saya')
@section('content')

    {{-- Header dengan Gradient --}}
    <div class="bg-primary bg-gradient text-white p-4 rounded-4 shadow-lg mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1"><i class="bi bi-garage me-2"></i>Garasi Kendaraan</h3>
                <p class="mb-0 opacity-75">Kelola QR Code dan lihat detail kendaraan Anda</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-light text-primary fs-6 px-3 py-2 shadow-sm">
                    <i class="bi bi-collection me-1"></i> {{ $kendaraans->count() }} Kendaraan
                </span>
            </div>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    @if($kendaraans->isNotEmpty())
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-white bg-opacity-75 rounded-4 p-3 text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h6 class="text-muted mt-2">Aktif</h6>
                    <h4 class="fw-bold">{{ $kendaraans->where('status', 1)->count() }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-white bg-opacity-75 rounded-4 p-3 text-center">
                    <i class="bi bi-qr-code fs-2 text-primary"></i>
                    <h6 class="text-muted mt-2">QR Aktif</h6>
                    <h4 class="fw-bold">{{ $kendaraans->whereNotNull('qr_token')->where('qr_expired_at', '>', now())->count() }}
                    </h4>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-white bg-opacity-75 rounded-4 p-3 text-center">
                    <i class="bi bi-car-front fs-2 text-warning"></i>
                    <h6 class="text-muted mt-2">Mobil</h6>
                    <h4 class="fw-bold">{{ $kendaraans->where('tipe', 'mobil')->count() }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-white bg-opacity-75 rounded-4 p-3 text-center">
                    <i class="bi bi-bicycle fs-2 text-info"></i>
                    <h6 class="text-muted mt-2">Motor</h6>
                    <h4 class="fw-bold">{{ $kendaraans->where('tipe', 'motor')->count() }}</h4>
                </div>
            </div>
        </div>
    @endif

    {{-- Daftar Kendaraan --}}
    @if($kendaraans->isEmpty())
        <div class="card border-0 shadow-sm bg-light rounded-4">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-emoji-frown text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">Belum Ada Kendaraan</h5>
                <p class="text-muted small">Silakan hubungi admin untuk mendaftarkan kendaraan Anda.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($kendaraans as $k)
                <div class="col-xl-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100 vehicle-card" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $k->plat_nomor }}</h5>
                                    <div>
                                        <span class="badge bg-{{ $k->tipe == 'mobil' ? 'primary' : 'success' }} me-1">
                                            <i
                                                class="bi bi-{{ $k->tipe == 'mobil' ? 'car-front' : 'bicycle' }} me-1"></i>{{ ucfirst($k->tipe) }}
                                        </span>
                                        @if($k->status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-pill" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                onclick="showQR('{{ $k->id }}', '{{ $k->plat_nomor }}')">
                                                <i class="bi bi-qr-code me-2"></i> Lihat QR
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="copyQR('{{ $k->id }}')">
                                                <i class="bi bi-clipboard me-2"></i> Salin QR
                                            </a>
                                        </li>
                                        @if($k->qr_token && (!$k->qr_expired_at || \Carbon\Carbon::parse($k->qr_expired_at)->isFuture()))
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="downloadQR('{{ $k->id }}', '{{ $k->plat_nomor }}')">
                                                    <i class="bi bi-download me-2"></i> Unduh QR
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-tag me-1"></i>Merk</small>
                                    <p class="mb-0 fw-bold">{{ $k->merk }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-palette me-1"></i>Warna</small>
                                    <p class="mb-0 fw-bold">{{ $k->warna }}</p>
                                </div>
                            </div>
                            @if($k->qr_expired_at)
                                <div class="mt-2">
                                    @php $expDate = \Carbon\Carbon::parse($k->qr_expired_at); @endphp
                                    <div class="d-flex align-items-center small">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        <span class="me-1">QR berlaku sampai</span>
                                        <strong>{{ $expDate->format('d M Y') }}</strong>
                                        <span class="ms-2 badge bg-{{ $expDate->isPast() ? 'danger' : 'success' }}">
                                            {{ $expDate->isPast() ? 'Kadaluwarsa' : 'Aktif' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light bg-opacity-50 d-flex justify-content-between align-items-center py-2 px-4">
                            <small class="text-muted">ID: #{{ $k->id }}</small>
                            <button class="btn btn-sm btn-outline-primary rounded-pill"
                                onclick="showQR('{{ $k->id }}', '{{ $k->plat_nomor }}')">
                                <i class="bi bi-qr-code me-1"></i> QR
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @include('partials.qr-modal')

@endsection

@push('styles')
    <style>
        .text-gradient {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .card-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Fungsi untuk menyalin QR data ke clipboard (jika diperlukan)
        function copyQR(id) {
            $.get('/kendaraan/' + id + '/qr', function (res) {
                if (res.success) {
                    navigator.clipboard.writeText(res.qr_data).then(function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'QR Code berhasil disalin!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }, function () {
                        Swal.fire('Gagal', 'Tidak dapat menyalin QR', 'error');
                    });
                }
            }).fail(function (xhr) {
                Swal.fire('Error', 'Gagal mengambil QR', 'error');
            });
        }
    </script>
@endpush