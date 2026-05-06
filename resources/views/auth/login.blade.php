@extends('layouts.guest')
@section('content')

    <div class="container-fluid vh-100">
        <div class="row h-100">
            {{-- Bagian Kiri: Branding + Animasi --}}
            <div
                class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center bg-primary bg-gradient text-white p-5">
                <div class="text-center">
                    {{-- Animasi Kendaraan (tampak samping) --}}
                    <div class="vehicle-animation mb-4">
                        <div class="road"></div>
                        <div class="car-wrapper">
                            <span class="vehicle-icon car">🚗</span>
                        </div>
                        <div class="motorcycle-wrapper">
                            <span class="vehicle-icon motorcycle">🏍️</span>
                        </div>
                    </div>
                    <h1 class="fw-bold display-4 mb-3">Parkir Kampus</h1>
                    <p class="lead mb-4">Solusi parkir modern untuk kampus Anda.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <span class="badge bg-light text-primary px-3 py-2 fs-6">
                            <i class="bi bi-shield-check me-2"></i>Aman
                        </span>
                        <span class="badge bg-light text-primary px-3 py-2 fs-6">
                            <i class="bi bi-lightning-charge me-2"></i>Cepat
                        </span>
                        <span class="badge bg-light text-primary px-3 py-2 fs-6">
                            <i class="bi bi-qr-code-scan me-2"></i>QR
                        </span>
                    </div>
                    <hr class="my-4 border-white-50 w-50 mx-auto">
                    <p class="text-white-50 small mb-0">
                        <i class="bi bi-building me-1"></i> Sistem Informasi Parkir Terintegrasi
                    </p>
                </div>
            </div>

            {{-- Bagian Kanan: Form Login --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light p-4">
                <div class="w-100" style="max-width: 420px;">
                    <div class="text-center mb-4 d-lg-none">
                        <div class="vehicle-animation mb-3">
                            <div class="road"></div>
                            <div class="car-wrapper"><span class="vehicle-icon car">🚗</span></div>
                            <div class="motorcycle-wrapper"><span class="vehicle-icon motorcycle">🏍️</span></div>
                        </div>
                        <h4 class="fw-bold">Parkir Kampus</h4>
                        <p class="text-muted">Silakan masuk ke akun Anda</p>
                    </div>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
                                <p class="text-muted small">Masuk untuk melanjutkan</p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-bold text-secondary">ALAMAT EMAIL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-envelope text-muted"></i>
                                        </span>
                                        <input type="email"
                                            class="form-control border-start-0 @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="nama@kampus.ac.id" required autofocus>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-bold text-secondary">PASSWORD</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-lock text-muted"></i>
                                        </span>
                                        <input type="password"
                                            class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="••••••••" required>
                                        <button class="btn btn-white border border-start-0" type="button"
                                            id="togglePassword">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                        <label class="form-check-label small" for="remember_me">Ingat saya</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="small text-decoration-none">Lupa
                                            password?</a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3 small text-muted">
                        &copy; {{ date('Y') }} Parkir Kampus. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <style>
        .vehicle-animation {
            position: relative;
            width: 100%;
            max-width: 320px;
            height: 100px;
            margin: 0 auto;
            overflow: hidden;
        }

        .road {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            height: 4px;
            background: repeating-linear-gradient(to right,
                    rgba(255, 255, 255, 0.9) 0px,
                    rgba(255, 255, 255, 0.9) 15px,
                    transparent 15px,
                    transparent 25px);
            animation: moveRoad 0.3s linear infinite;
            z-index: 1;
        }

        @keyframes moveRoad {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(25px);
            }
        }

        .car-wrapper,
        .motorcycle-wrapper {
            position: absolute;
            bottom: 25px;
            z-index: 2;
        }

        .car-wrapper {
            left: -60px;
            animation: driveCar 3s infinite linear;
        }

        .motorcycle-wrapper {
            right: -60px;
            animation: driveMotorcycle 3s infinite linear;
            animation-delay: 1.5s;
        }

        .vehicle-icon {
            font-size: 3rem;
            /* emoji lebih besar */
            filter: drop-shadow(0 6px 8px rgba(0, 0, 0, 0.3));
            display: block;
        }

        @keyframes driveCar {
            0% {
                transform: translateX(0);
            }

            40% {
                transform: translateX(calc(320px + 60px));
            }

            50% {
                transform: translateX(calc(320px + 60px));
            }

            50.01% {
                transform: translateX(-60px);
            }

            100% {
                transform: translateX(0);
            }
        }

        @keyframes driveMotorcycle {
            0% {
                transform: translateX(0);
            }

            40% {
                transform: translateX(calc(-320px - 60px));
            }

            50% {
                transform: translateX(calc(-320px - 60px));
            }

            50.01% {
                transform: translateX(60px);
            }

            100% {
                transform: translateX(0);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            if (!togglePassword || !passwordInput) return;
            const icon = togglePassword.querySelector('i');
            if (!icon) return;

            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                if (isPassword) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    </script>
@endsection