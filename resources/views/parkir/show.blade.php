@extends('layouts.app')
@section('title', 'Detail Parkir')
@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Parkir #{{ $parkir->id }}</h5>
                    @php
                        $role = auth()->user()->role->slug;
                        $backRoute = match ($role) {
                            'petugas' => route('petugas.parkir'),
                            'mahasiswa' => route('mahasiswa.parkir'),
                            default => route('parkir.index'),
                        };
                    @endphp
                    <a href="{{ $backRoute }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Plat Nomor</th>
                            <td><strong>{{ $parkir->kendaraan->plat_nomor ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Pemilik</th>
                            <td>{{ $parkir->kendaraan->user->name ?? 'N/A' }} ({{ $parkir->kendaraan->user->nim ?? '-' }})
                            </td>
                        </tr>
                        <tr>
                            <th>Kendaraan</th>
                            <td>{{ $parkir->kendaraan->merk ?? '' }} {{ $parkir->kendaraan->warna ?? '' }}
                                ({{ $parkir->kendaraan->tipe ?? '' }})</td>
                        </tr>
                        <tr>
                            <th>Check-in</th>
                            <td>{{ $parkir->check_in->format('d F Y, H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Check-out</th>
                            <td>{{ $parkir->check_out ? $parkir->check_out->format('d F Y, H:i:s') : 'Belum check-out' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>{{ $parkir->durasi ? $parkir->durasi . ' menit' : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span
                                    class="badge bg-{{ $parkir->status == 'active' ? 'warning' : ($parkir->status == 'completed' ? 'success' : 'danger') }}">
                                    {{ $parkir->status == 'active' ? 'Aktif' : ($parkir->status == 'completed' ? 'Selesai' : 'Pelanggaran') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Petugas Scan</th>
                            <td>{{ $parkir->petugas->name ?? 'Self-service' }}</td>
                        </tr>
                        <tr>
                            <th>Device Info</th>
                            <td>
                                @if($parkir->scan_device_info)
                                    <small class="text-muted">
                                        IP: {{ $parkir->scan_device_info['ip'] ?? '-' }}<br>
                                        Browser: {{ $parkir->scan_device_info['user_agent'] ?? '-' }}
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>QR Hash</th>
                            <td><small class="text-muted">{{ $parkir->qr_data_hash ?? '-' }}</small></td>
                        </tr>
                    </table>

                    {{-- Tombol Tandai Pelanggaran --}}
                    @if($parkir->status !== 'violation')
                        <div class="mt-3">
                            <form action="{{ route('parkir.markViolation', $parkir->id) }}" method="POST"
                                onsubmit="return confirm('Tandai sebagai pelanggaran?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Tandai Pelanggaran
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection