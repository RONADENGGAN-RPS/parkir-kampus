@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="bi bi-sliders"></i> Pengaturan Sistem Parkir</h5>
        </div>
        <div class="card-body">
            <form id="settingsForm">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">🅿️ Kapasitas Parkir Maksimal</label>
                    <input type="number" class="form-control" name="kapasitas_parkir"
                        value="{{ $settings['kapasitas_parkir']->value ?? 100 }}" required min="1">
                    <small class="text-muted">Jumlah maksimal kendaraan yang bisa parkir bersamaan.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">⏱️ Batas Durasi Parkir (menit)</label>
                    <input type="number" class="form-control" name="max_parking_duration"
                        value="{{ $settings['max_parking_duration']->value ?? 300 }}" required min="1">
                    <small class="text-muted">Parkir melebihi durasi ini akan otomatis ditandai
                        <strong>Pelanggaran</strong>.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">📆 Masa Berlaku QR Code (hari)</label>
                    <input type="number" class="form-control" name="qr_expiry_days"
                        value="{{ $settings['qr_expiry_days']->value ?? 365 }}" required min="1">
                    <small class="text-muted">QR Code kendaraan akan kadaluwarsa setelah jumlah hari ini.</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('#settingsForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('settings.update') }}',
                method: 'PATCH',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                    }
                },
                error: function (xhr) {
                    let msg = 'Gagal menyimpan pengaturan';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    </script>
@endsection