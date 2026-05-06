@extends('layouts.app')
@section('title', 'Backup & Restore Database')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Backup & Restore Database</h4>
        <button class="btn btn-primary" id="createBackupBtn">
            <i class="bi bi-cloud-upload"></i> Buat Backup Sekarang
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($backups->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="mt-3">Belum ada file backup. <br>Klik tombol <strong>"Buat Backup Sekarang"</strong> untuk membuat
                        backup pertama.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama File</th>
                                <th>Ukuran (KB)</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="backupList">
                            @foreach($backups as $backup)
                                <tr>
                                    <td><code>{{ $backup['name'] }}</code></td>
                                    <td>{{ $backup['size'] }} KB</td>
                                    <td>{{ $backup['modified'] }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('backup.download', $backup['name']) }}" class="btn btn-success"
                                                title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button class="btn btn-danger" onclick="confirmDelete('{{ $backup['name'] }}')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="btn btn-warning" onclick="confirmRestore('{{ $backup['name'] }}')"
                                                title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Informasi Penting --}}
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i>
        <strong>Catatan:</strong> Backup disimpan di folder <code>storage/backup/database/</code>. Pastikan fitur
        <code>mysqldump</code> aktif di server XAMPP Anda.
    </div>

@endsection

@section('scripts')
    <script>
        const createBtn = document.getElementById('createBackupBtn');

        createBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Membuat Backup...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('backup.create') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            location.reload(); // reload untuk melihat file baru
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server', 'error');
                    console.error(err);
                });
        });

        function confirmDelete(filename) {
            Swal.fire({
                title: 'Hapus backup?',
                text: filename,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/backup/delete/' + filename, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Dihapus!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        });
                }
            });
        }

        function confirmRestore(filename) {
            Swal.fire({
                title: '⚠️ Restore Database?',
                html: `<p>Anda akan mengembalikan database dari <strong>${filename}</strong>.</p>
                       <p class="text-danger fw-bold">Semua data saat ini akan hilang dan diganti!</p>
                       <input id="confirmText" class="swal2-input" placeholder="Ketik YA untuk melanjutkan">`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Restore Sekarang',
                confirmButtonColor: '#d33',
                preConfirm: () => {
                    const val = document.getElementById('confirmText').value;
                    if (val !== 'YA') {
                        Swal.showValidationMessage('Anda harus mengetik YA (huruf kapital)');
                    }
                    return val;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/backup/restore/' + filename, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ confirm: result.value })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Sukses!', data.message, 'success');
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
@endsection