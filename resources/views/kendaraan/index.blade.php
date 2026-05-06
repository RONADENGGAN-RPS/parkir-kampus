@extends('layouts.app')
@section('title', 'Data Kendaraan')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Data Kendaraan</h4>
        <button class="btn btn-primary" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#kendaraanModal">
            <i class="bi bi-plus-lg"></i> Tambah Kendaraan
        </button>
    </div>

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle"id="kendaraanTable">
                <thead class="table-light">
                    <tr>
                        <th>Plat Nomor</th>
                        <th>Tipe</th>
                        <th>Merk / Warna</th>
                        <th>Pemilik</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kendaraans as $k)
                        <tr>
                            <td><strong>{{ $k->plat_nomor }}</strong></td>
                            <td><span class="badge bg-{{ $k->tipe == 'mobil' ? 'primary' : 'success' }}">{{ $k->tipe }}</span></td>
                            <td>{{ $k->merk }} / {{ $k->warna }}</td>
                            <td>{{ $k->user->name ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-{{ $k->status ? 'success' : 'secondary' }} toggle-status"
                                    data-id="{{ $k->id }}" title="Klik untuk ubah status">
                                    {{ $k->status ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="showQR('{{ $k->id }}', '{{ $k->plat_nomor }}')" title="QR Code">
                                        <i class="bi bi-qr-code"></i>
                                    </button>
                                    <button class="btn btn-warning" onclick="editKendaraan('{{ $k->id }}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteKendaraan('{{ $k->id }}', '{{ $k->plat_nomor }}')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT --}}
    <div class="modal fade" id="kendaraanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="kendaraanForm">
                    @csrf
                    <input type="hidden" id="kendaraanId" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Kendaraan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Pemilik (Mahasiswa)</label>
                            <select class="form-select select2-pemilik" name="user_id" id="user_id" required>
                                <option value="">-- Cari Nama / NIM --</option>
                                @foreach($mahasiswas as $mhs)
                                    <option value="{{ $mhs->id }}">{{ $mhs->name }} ({{ $mhs->nim }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Plat Nomor</label>
                            <input type="text" name="plat_nomor" id="plat_nomor" class="form-control" placeholder="B 1234 XYZ" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tipe</label>
                                <select name="tipe" id="tipe" class="form-select" required onchange="updateMerkSuggestions()">
                                    <option value="motor">Motor</option>
                                    <option value="mobil">Mobil</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Merk</label>
                                <input type="text" name="merk" id="merk" class="form-control" list="merkList" placeholder="Ketik atau pilih" required>
                                <datalist id="merkList"></datalist>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Warna</label>
                            <input type="text" name="warna" id="warna" class="form-control" placeholder="Hitam" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL QR --}}
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5>QR Code Kendaraan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="qrCodeContainer" class="d-flex justify-content-center"></div>
                    <p class="mt-2 fw-bold" id="qrPlatText"></p>
                    <small class="text-muted">Scan untuk check-in/out</small>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- Select2 CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTables
            $('#kendaraanTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    zeroRecords: "Tidak ada data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
                }
            });

            // Select2 untuk pemilik
            $('.select2-pemilik').select2({
                placeholder: "Ketik nama / NIM...",
                dropdownParent: $('#kendaraanModal'),
                width: '100%'
            });

            // Update saran merk saat tipe berubah
            updateMerkSuggestions();
        });

        // Daftar merk yang umum
        const mobilMerk = ['Toyota','Honda','Suzuki','Mitsubishi','Daihatsu','Nissan','Wuling','Hyundai','BMW','Mercedes-Benz'];
        const motorMerk = ['Honda','Yamaha','Suzuki','Kawasaki','TVS','Piaggio','Vespa'];

        function updateMerkSuggestions() {
            const tipe = $('#tipe').val();
            const dataList = document.getElementById('merkList');
            dataList.innerHTML = '';
            const list = tipe === 'mobil' ? mobilMerk : motorMerk;
            list.forEach(m => {
                const option = document.createElement('option');
                option.value = m;
                dataList.appendChild(option);
            });
        }

        function resetForm() {
            $('#kendaraanForm')[0].reset();
            $('#kendaraanId').val('');
            $('#modalTitle').text('Tambah Kendaraan');
            $('#methodField').remove();
            // Reset Select2
            $('#user_id').val('').trigger('change');
            updateMerkSuggestions();
        }

        function editKendaraan(id) {
            $.get('/kendaraan/' + id + '/edit', function(data) {
                $('#modalTitle').text('Edit Kendaraan');
                $('#kendaraanId').val(data.id);
                $('#user_id').val(data.user_id).trigger('change');
                $('#plat_nomor').val(data.plat_nomor);
                $('#tipe').val(data.tipe);
                updateMerkSuggestions();
                $('#merk').val(data.merk);
                $('#warna').val(data.warna);

                if ($('#methodField').length === 0) {
                    $('#kendaraanForm').append('<input type="hidden" id="methodField" name="_method" value="PUT">');
                } else {
                    $('#methodField').val('PUT');
                }
                $('#kendaraanModal').modal('show');
            });
        }

        $('#kendaraanForm').submit(function(e) {
            e.preventDefault();
            const id = $('#kendaraanId').val();
            const url = id ? '/kendaraan/' + id : '/kendaraan';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#kendaraanModal').modal('hide');
                        setTimeout(() => location.reload(), 500);
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Toggle status
        $(document).on('click', '.toggle-status', function() {
            const btn = $(this);
            const id = btn.data('id');
            $.ajax({
                url: '/kendaraan/' + id + '/toggle-status',
                method: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        btn.text(res.status ? 'Aktif' : 'Nonaktif');
                        btn.toggleClass('btn-success btn-secondary');
                        Swal.fire('Status', res.message, 'success');
                    }
                }
            });
        });

        function deleteKendaraan(id, plat) {
            Swal.fire({
                title: 'Hapus ' + plat + '?',
                text: "Data akan dihapus sementara.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/kendaraan/' + id,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Dihapus!', res.message, 'success');
                            setTimeout(() => location.reload(), 500);
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Tidak dapat menghapus.', 'error');
                        }
                    });
                }
            });
        }

        function showQR(id, plat) {
            $('#qrPlatText').text(plat);
            $('#qrCodeContainer').html('');
            $.get('/kendaraan/' + id + '/qr', function(res) {
                if (res.success) {
                    QRCode.toCanvas(document.createElement('canvas'), res.qr_data, { width: 200 }, function(error, canvas) {
                        if (error) {
                            $('#qrCodeContainer').html('<div class="alert alert-danger">Gagal generate QR</div>');
                        } else {
                            $('#qrCodeContainer').html(canvas);
                        }
                    });
                    $('#qrModal').modal('show');
                }
            });
        }

        $('#kendaraanModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    </script>
@endsection