@extends('layouts.app')
@section('title', 'Riwayat Parkir')
@section('content')

    <h4>Riwayat Parkir</h4>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Plat</th>
                    <th>Status</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parkirs as $p)
                    <tr>
                        <td>{{ $p->kendaraan->plat_nomor ?? '-' }}</td>
                        <td>
                            <span
                                class="badge bg-{{ $p->status == 'active' ? 'warning' : ($p->status == 'completed' ? 'success' : 'danger') }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td>{{ $p->check_in->format('d/m/Y H:i') }}</td>
                        <td>{{ $p->check_out ? $p->check_out->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $p->durasi ? $p->durasi . ' mnt' : '-' }}</td>
                        <td>
                            <a href="{{ route('parkir.show', $p->id) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada riwayat parkir</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $parkirs->links() }}

@endsection