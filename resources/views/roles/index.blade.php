@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('content')

    @php
        $isSuperadmin = auth()->user()->role->slug === 'superadmin';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Roles & Permissions</h4>
        @if($isSuperadmin)
            <button class="btn btn-primary" onclick="resetRoleForm()" data-bs-toggle="modal" data-bs-target="#roleModal">
                <i class="bi bi-plus-lg"></i> Tambah Role
            </button>
        @endif
    </div>

    <div class="row g-3">
        @foreach($roles as $role)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            {{ $role->name }}
                            <small class="text-muted">({{ $role->slug }})</small>
                        </h5>
                        @if($isSuperadmin)
                            <div>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editRole('{{ $role->id }}', '{{ $role->name }}', '{{ $role->slug }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if(!in_array($role->slug, ['superadmin', 'admin', 'petugas', 'mahasiswa']))
                                    <button class="btn btn-sm btn-danger" onclick="deleteRole('{{ $role->id }}', '{{ $role->name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <form onchange="syncPermissions('{{ $role->id }}', this)" data-role="{{ $role->id }}">
                            @csrf
                            @foreach($permissions as $module => $perms)
                                <div class="mb-2">
                                    <strong class="text-uppercase small">{{ $module }}</strong>
                                    <div class="row">
                                        @foreach($perms as $perm)
                                            <div class="col-6 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $perm->id }}" {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label small">{{ $perm->action }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL TAMBAH / EDIT ROLE --}}
    @if($isSuperadmin)
        <div class="modal fade" id="roleModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="roleForm">
                        @csrf
                        <input type="hidden" id="roleId">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roleModalTitle">Tambah Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Role</label>
                                <input type="text" name="name" id="roleName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Slug</label>
                                <input type="text" name="slug" id="roleSlug" class="form-control" required>
                                <small class="text-muted">Contoh: superadmin, admin, petugas</small>
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
    @if($isSuperadmin)
        <script>
            // ROLE CRUD
            function resetRoleForm() {
                $('#roleForm')[0].reset();
                $('#roleId').val('');
                $('#roleModalTitle').text('Tambah Role');
                $('#methodField').remove();
            }

            function editRole(id, name, slug) {
                $('#roleModalTitle').text('Edit Role');
                $('#roleId').val(id);
                $('#roleName').val(name);
                $('#roleSlug').val(slug);
                if ($('#methodField').length === 0) {
                    $('#roleForm').append('<input type="hidden" id="methodField" name="_method" value="PUT">');
                }
                $('#roleModal').modal('show');
            }

            $('#roleForm').submit(function (e) {
                e.preventDefault();
                const id = $('#roleId').val();
                const url = id ? '/roles/' + id : '/roles';
                const method = id ? 'PUT' : 'POST';
                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success');
                            $('#roleModal').modal('hide');
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function deleteRole(id, name) {
                Swal.fire({
                    title: `Hapus role ${name}?`,
                    text: "Semua user dengan role ini akan kehilangan aksesnya.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/roles/' + id,
                            method: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.success) { Swal.fire('Dihapus!', res.message, 'success'); setTimeout(() => location.reload(), 500); }
                                else { Swal.fire('Gagal', res.message, 'error'); }
                            }
                        });
                    }
                });
            }

            function syncPermissions(roleId, formElement) {
                const formData = new FormData(formElement);
                const permissions = formData.getAll('permissions[]');
                $.ajax({
                    url: `/roles/${roleId}/sync-permissions`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', permissions: permissions },
                    success: function (res) {
                        if (res.success) Swal.fire({ icon: 'success', title: res.message, timer: 1000, showConfirmButton: false });
                    }
                });
            }

            $('#roleModal').on('hidden.bs.modal', function () { resetRoleForm(); });
        </script>
    @endif
@endsection