@extends('layouts.app')
@section('title', 'Profil')
@section('content')

    <div class="row justify-content-center">
        {{-- Kolom Kiri: Informasi Profil --}}
        <div class="col-lg-8">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Profil berhasil diperbarui.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Card Informasi Dasar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Profil</h5>
                    <span class="badge bg-primary fs-6">{{ $user->role->name ?? 'Tanpa Role' }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle" width="100"
                                        height="100" alt="Avatar">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                        style="width:100px;height:100px; font-size:2rem; color:white;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <label for="avatarInput"
                                    class="btn btn-sm btn-outline-secondary position-absolute bottom-0 end-0 rounded-circle"
                                    title="Ganti avatar">
                                    <i class="bi bi-camera"></i>
                                </label>
                            </div>
                            <div class="mt-2 small text-muted">Klik ikon kamera</div>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="130">Nama</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>NIM</th>
                                    <td>{{ $user->nim ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No. HP</th>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td><span class="badge bg-info">{{ $user->role->name ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><span
                                            class="badge bg-{{ $user->active ? 'success' : 'danger' }}">{{ $user->active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Statistik login --}}
                    <div class="border rounded p-3 bg-light">
                        <h6 class="fw-bold mb-2"><i class="bi bi-shield-lock me-1"></i>Keamanan & Login</h6>
                        <div class="row small">
                            <div class="col-md-6">
                                <span class="text-muted">Terakhir login:</span><br>
                                <strong>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">IP terakhir:</span><br>
                                <strong>{{ $user->last_login_ip ?? '-' }}</strong>
                            </div>
                            <div class="col-md-6 mt-2">
                                <span class="text-muted">Percobaan login:</span><br>
                                <strong>{{ $user->login_attempts }}</strong>
                            </div>
                            <div class="col-md-6 mt-2">
                                <span class="text-muted">Akun terkunci sampai:</span><br>
                                <strong>{{ $user->locked_until ? $user->locked_until->format('d M Y H:i') : 'Tidak' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Login Pribadi --}}
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-list-ul me-1"></i>Riwayat Login Saya</h6>
                        @php
                            $recentLogins = \App\Models\LoginHistory::where('user_id', $user->id)
                                ->latest()
                                ->limit(10)
                                ->get();
                        @endphp

                        @if($recentLogins->isEmpty())
                            <p class="text-muted">Belum ada riwayat login.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>IP Address</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentLogins as $log)
                                            <tr>
                                                <td>
                                                    @if($log->action === 'login')
                                                        <span class="badge bg-success">Login</span>
                                                    @else
                                                        <span class="badge bg-danger">Gagal</span>
                                                    @endif
                                                </td>
                                                <td><code>{{ $log->ip_address }}</code></td>
                                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Role & Aksi --}}
        <div class="col-lg-4">
            {{-- Detail Role --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Detail Role</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Slug:</strong> <code>{{ $user->role->slug ?? '-' }}</code></p>
                    @if($user->role)
                        <p class="mb-0"><strong>Permissions:</strong></p>
                        <div class="mt-2">
                            @forelse($user->role->permissions->take(10) as $perm)
                                <span class="badge bg-secondary me-1 mb-1">{{ $perm->module }}:{{ $perm->action }}</span>
                            @empty
                                <span class="text-muted">Tidak ada permission</span>
                            @endforelse
                            @if($user->role->permissions->count() > 10)
                                <span class="badge bg-light text-dark">+{{ $user->role->permissions->count() - 10 }} lainnya</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Toggle Aksi: Edit Profil & Ganti Password --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center" id="toggleActions"
                    role="button" data-bs-toggle="collapse" data-bs-target="#actionsCollapse" aria-expanded="false"
                    aria-controls="actionsCollapse" style="cursor: pointer;">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Aksi</h6>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </div>
                <div class="collapse" id="actionsCollapse">
                    <div class="card-body d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditProfile">
                            <i class="bi bi-pencil me-2"></i>Edit Profil
                        </button>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#modalChangePassword">
                            <i class="bi bi-lock me-2"></i>Ganti Password
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hapus Akun (opsional) --}}
            @if($user->role->slug !== 'superadmin')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom text-danger">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Hapus Akun</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Setelah akun dihapus, semua data akan hilang permanen.</p>
                        <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                            <i class="bi bi-trash me-1"></i> Hapus Akun Saya
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Edit Profil --}}
    <div class="modal fade" id="modalEditProfile" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*"
                        onchange="previewAvatar(this)">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}"
                                    class="rounded-circle {{ $user->avatar ? '' : 'd-none' }}" width="80" height="80"
                                    alt="Avatar">
                                <div id="avatarPlaceholder"
                                    class="rounded-circle bg-secondary d-flex align-items-center justify-content-center {{ $user->avatar ? 'd-none' : '' }}"
                                    style="width:80px;height:80px; font-size:1.5rem; color:white;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <label for="avatarInput"
                                    class="btn btn-sm btn-outline-secondary position-absolute bottom-0 end-0 rounded-circle"
                                    title="Unggah foto">
                                    <i class="bi bi-camera"></i>
                                </label>
                            </div>
                            <div class="small text-muted mt-1">Klik ikon kamera untuk ganti</div>
                        </div>

                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editName" name="name" value="{{ $user->name }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" value="{{ $user->email }}"
                                required>
                        </div>
                        @if($user->role->slug === 'mahasiswa')
                            <div class="mb-3">
                                <label for="editNim" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="editNim" name="nim" value="{{ $user->nim }}">
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" value="{{ $user->phone }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Ganti Password --}}
    <div class="modal fade" id="modalChangePassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Perbarui Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Masukkan password Anda untuk menghapus akun.</p>
                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password_delete" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Toggle icon rotation
        const toggleIcon = document.querySelector('.toggle-icon');
        const actionsCollapse = document.getElementById('actionsCollapse');
        actionsCollapse.addEventListener('show.bs.collapse', () => toggleIcon.classList.replace('bi-chevron-down', 'bi-chevron-up'));
        actionsCollapse.addEventListener('hide.bs.collapse', () => toggleIcon.classList.replace('bi-chevron-up', 'bi-chevron-down'));

        // Avatar preview
        function previewAvatar(input) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const placeholder = document.getElementById('avatarPlaceholder');
                const preview = document.getElementById('avatarPreview');
                if (placeholder) placeholder.classList.add('d-none');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }

        document.querySelector('[for="avatarInput"]').addEventListener('click', function () {
            document.getElementById('avatarInput').click();
        });

        // Hapus otomatis reset preview saat modal edit ditutup
        const modalEdit = document.getElementById('modalEditProfile');
        modalEdit.addEventListener('hidden.bs.modal', function () {
            const preview = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');
            const hasAvatar = '{{ $user->avatar }}' !== '';
            if (hasAvatar) {
                preview.src = '{{ asset('storage/' . $user->avatar) }}';
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            } else {
                preview.classList.add('d-none');
                if (placeholder) placeholder.classList.remove('d-none');
            }
            document.getElementById('avatarInput').value = '';
        });
    </script>
@endsection