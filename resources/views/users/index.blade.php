@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')

    @php
        $canCreate = $userPerms->contains('create');
        $canEdit = $userPerms->contains('update');
        $canDelete = $userPerms->contains('delete');
        $isSuperadmin = auth()->user()->role->slug === 'superadmin';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manajemen Pengguna</h4>
        @if($canCreate)
            <button class="btn btn-primary" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#userModal">
                <i class="bi bi-plus-lg"></i> Tambah Pengguna
            </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, NIM..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-5">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIM</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->nim ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ optional($u->role)->name ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge bg-{{ $u->active ? 'success' : 'secondary' }}">
                                    {{ $u->active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($canEdit)
                                        <button class="btn btn-warning" onclick="editUser('{{ $u->id }}')" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-info" onclick="resetPassword('{{ $u->id }}', '{{ $u->name }}')"
                                            title="Reset Password">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    @endif
                                    @if($canDelete && !(optional($u->role)->slug === 'superadmin' && !$isSuperadmin))
                                        <button class="btn btn-danger" onclick="deleteUser('{{ $u->id }}', '{{ $u->name }}')"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    @if($canCreate || $canEdit)
        <div class="modal fade" id="userModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="userForm">
                        @csrf
                        <input type="hidden" id="userId" name="id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>NIM (opsional)</label>
                                <input type="text" name="nim" id="nim" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role_id" id="role_id" class="form-select" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        {{-- Admin tidak bisa pilih Super Admin --}}
                                        @if($role->slug !== 'superadmin' || $isSuperadmin)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" id="password" class="form-control" minlength="6">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah (saat edit).</small>
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
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            new DataTable('#usersTable', {
                responsive: true,
                language: {
                    search: "Cari:",
                    zeroRecords: "Tidak ada data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
                }
            });
        });

        @if($canCreate || $canEdit)
            function resetForm() {
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#modalTitle').text('Tambah Pengguna');
                $('#password').attr('required', true);
                $('#methodField').remove();
            }

            function editUser(id) {
                $.get('/users/' + id + '/edit', function (data) {
                    $('#modalTitle').text('Edit Pengguna');
                    $('#userId').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#nim').val(data.nim);
                    $('#role_id').val(data.role_id);
                    $('#password').val('').attr('required', false);

                    if ($('#methodField').length === 0) {
                        $('#userForm').append('<input type="hidden" id="methodField" name="_method" value="PUT">');
                    }
                    $('#userModal').modal('show');
                });
            }

            $('#userForm').submit(function (e) {
                e.preventDefault();
                const id = $('#userId').val();
                const url = id ? '/users/' + id : '/users';
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success');
                            $('#userModal').modal('hide');
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function resetPassword(id, name) {
                Swal.fire({
                    title: 'Reset Password',
                    text: `Reset password ${name} ke "password"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/users/' + id + '/reset-password',
                            method: 'PATCH',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                Swal.fire('Berhasil', res.message, 'success');
                            }
                        });
                    }
                });
            }
        @endif

            @if($canDelete)
                function deleteUser(id, name) {
                    Swal.fire({
                        title: 'Hapus ' + name + '?',
                        text: "Data akan dihapus sementara.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/users/' + id,
                                method: 'DELETE',
                                data: { _token: '{{ csrf_token() }}' },
                                success: function (res) {
                                    if (res.success) {
                                        Swal.fire('Dihapus!', res.message, 'success');
                                        setTimeout(() => location.reload(), 500);
                                    } else {
                                        Swal.fire('Gagal', res.message, 'error');
                                    }
                                },
                                error: function (xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus', 'error');
                                }
                            });
                        }
                    });
                }
            @endif

        $('#userModal').on('hidden.bs.modal', function () {
            resetForm();
        });
    </script>
@endsection