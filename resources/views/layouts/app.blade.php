<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Parkir Kampus'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        html,
        body {
            height: 100vh;
            overflow: hidden;
        }

        @media (max-width: 767.98px) {
            html,
            body {
                overflow-y: auto;
                height: auto;
            }
        }

        .sidebar-desktop {
            width: 260px;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .sidebar-desktop.collapsed {
            width: 70px !important;
            padding: 0.5rem !important;
        }

        .sidebar-desktop.collapsed .sidebar-header h4,
        .sidebar-desktop.collapsed .nav-link .menu-text {
            display: none;
        }

        .sidebar-desktop.collapsed .nav-link {
            justify-content: center;
        }

        .sidebar-desktop.collapsed .nav-link i {
            margin-right: 0 !important;
        }

        .sidebar-desktop.collapsed .sidebar-section {
            display: none;
        }

        .sidebar-desktop.collapsed .section-divider {
            margin: 0.5rem 0;
        }

        .nav-link {
            white-space: nowrap;
            align-items: center;
            display: flex;
            overflow: hidden;
        }

        .nav-link i {
            font-size: 1.25rem;
            transition: margin 0.3s;
        }

        .nav-link .menu-text {
            margin-left: 0.5rem;
            transition: all 0.3s;
        }

        .nav-link.active {
            font-weight: 600;
        }

        #sidebarToggle i {
            transition: transform 0.3s;
        }

        #sidebarToggle.rotated i {
            transform: rotate(180deg);
        }

        .content-main {
            height: 100vh;
            overflow-y: auto;
            flex: 1;
            transition: all 0.3s ease;
        }

        .content-main table {
            width: 100% !important;
        }

        .bottom-nav {
            z-index: 1030;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        [data-bs-theme="dark"] .bottom-nav {
            background: rgba(33, 37, 41, 0.95) !important;
        }

        .bottom-nav a {
            transition: color 0.2s;
        }

        .bottom-nav a:hover {
            color: var(--bs-primary);
        }

        .card {
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .border-warning {
            border-color: #ffc107 !important;
        }

        .border-danger {
            border-color: #dc3545 !important;
        }

        .border-info {
            border-color: #0dcaf0 !important;
        }

        .border-success {
            border-color: #198754 !important;
        }
    </style>
</head>

<body>
    @php
        $userRole = auth()->user()->role->slug ?? 'mahasiswa';
    @endphp

    <div class="d-flex">
        {{-- SIDEBAR DESKTOP --}}
        <nav id="sidebarDesktop"
            class="sidebar-desktop bg-body-tertiary border-end d-none d-md-flex flex-column p-3 shadow-sm">
            <div class="sidebar-header text-center mb-4">
                <span class="logo-icon d-none">🚗</span>
                <h4 class="fw-bold text-primary mb-0">🚗 Parkir Kampus</h4>
            </div>

            <ul class="nav flex-column">
                {{-- ========== SUPER ADMIN ========== --}}
                @if($userRole === 'superadmin')
                    <li class="nav-item">
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i> <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kendaraan.index') }}"
                            class="nav-link {{ request()->routeIs('kendaraan.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-car-front me-2"></i> <span class="menu-text">Kendaraan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-qr-code-scan me-2"></i> <span class="menu-text">Scan QR</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('parkir.index') }}"
                            class="nav-link {{ request()->routeIs('parkir.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> <span class="menu-text">Riwayat Parkir</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan') }}"
                            class="nav-link {{ request()->routeIs('laporan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> <span class="menu-text">Laporan</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3 sidebar-section">
                        <span class="nav-link disabled text-muted fw-bold small">ADMIN</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-people-fill me-2"></i> <span class="menu-text">Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('backup.index') }}"
                            class="nav-link {{ request()->routeIs('backup.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-cloud-upload me-2"></i> <span class="menu-text">Backup & Restore</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings') }}"
                            class="nav-link {{ request()->routeIs('settings') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-gear me-2"></i> <span class="menu-text">Pengaturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('log-aktivitas.index') }}"
                            class="nav-link {{ request()->routeIs('log-aktivitas.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-journal-text me-2"></i> <span class="menu-text">Log Aktivitas</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3 sidebar-section">
                        <span class="nav-link disabled text-muted fw-bold small">SUPER ADMIN</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}"
                            class="nav-link {{ request()->routeIs('roles.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> <span class="menu-text">Roles & Permissions</span>
                        </a>
                    </li>

                {{-- ========== ADMIN ========== --}}
                @elseif($userRole === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i> <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kendaraan.index') }}"
                            class="nav-link {{ request()->routeIs('kendaraan.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-car-front me-2"></i> <span class="menu-text">Kendaraan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-qr-code-scan me-2"></i> <span class="menu-text">Scan QR</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('parkir.index') }}"
                            class="nav-link {{ request()->routeIs('parkir.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> <span class="menu-text">Riwayat Parkir</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan') }}"
                            class="nav-link {{ request()->routeIs('laporan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> <span class="menu-text">Laporan</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3 sidebar-section">
                        <span class="nav-link disabled text-muted fw-bold small">ADMIN</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-people-fill me-2"></i> <span class="menu-text">Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('backup.index') }}"
                            class="nav-link {{ request()->routeIs('backup.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-cloud-upload me-2"></i> <span class="menu-text">Backup & Restore</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings') }}"
                            class="nav-link {{ request()->routeIs('settings') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-gear me-2"></i> <span class="menu-text">Pengaturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('log-aktivitas.index') }}"
                            class="nav-link {{ request()->routeIs('log-aktivitas.*') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-journal-text me-2"></i> <span class="menu-text">Log Aktivitas</span>
                        </a>
                    </li>

                {{-- ========== PETUGAS ========== --}}
                @elseif($userRole === 'petugas')
                    <li class="nav-item mt-3 sidebar-section">
                        <span class="nav-link disabled text-muted fw-bold small">PETUGAS</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('petugas.dashboard') }}"
                            class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i> <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-qr-code-scan me-2"></i> <span class="menu-text">Scan QR</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('petugas.parkir') }}"
                            class="nav-link {{ request()->routeIs('petugas.parkir') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> <span class="menu-text">Riwayat Parkir</span>
                        </a>
                    </li>

                {{-- ========== MAHASISWA ========== --}}
                @else
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i> <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.kendaraan') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.kendaraan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-car-front me-2"></i> <span class="menu-text">Kendaraan Saya</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.parkir') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.parkir') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> <span class="menu-text">Riwayat Parkir</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.laporan') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.laporan') ? 'active bg-primary text-white rounded' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> <span class="menu-text">Laporan</span>
                        </a>
                    </li>
                @endif
            </ul>

            <div class="mt-auto pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary w-100 btn-logout-trigger">
                    <i class="bi bi-box-arrow-right me-2"></i> <span class="menu-text">Logout</span>
                </button>
            </div>
        </nav>

        {{-- KONTEN UTAMA --}}
        <div class="content-main d-flex flex-column">
            {{-- HEADER --}}
            <header
                class="bg-body-tertiary border-bottom px-3 py-2 d-flex align-items-center justify-content-between shadow-sm sticky-top">
                <div class="d-flex align-items-center">
                    <button id="sidebarToggle"
                        class="btn btn-outline-secondary d-none d-md-inline-flex align-items-center me-2"
                        type="button"><i class="bi bi-list"></i></button>
                    <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasSidebar"><i class="bi bi-list fs-5"></i></button>
                    <span class="fw-bold">{{ config('app.name') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="darkModeToggle" class="btn btn-outline-secondary" title="Dark Mode"><i
                            class="bi bi-moon-stars"></i></button>
                    <span id="liveClock"
                        class="btn btn-outline-secondary border-0 px-2 d-none d-md-inline-flex align-items-center"
                        style="pointer-events: none; cursor: default;"><i class="bi bi-clock me-1"></i> <span
                            class="fw-bold">00:00</span></span>
                    <div class="dropdown d-none d-md-block">
                        <button class="btn btn-outline-secondary" type="button" id="calendarDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Kalender"><i
                                class="bi bi-calendar3"></i></button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="width: 280px;" id="calendarContainer">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <button class="btn btn-sm btn-outline-secondary" id="calPrev">&lsaquo;</button>
                                <span class="fw-bold" id="calMonthYear"></span>
                                <button class="btn btn-sm btn-outline-secondary" id="calNext">&rsaquo;</button>
                            </div>
                            <div class="d-grid" style="grid-template-columns: repeat(7, 1fr); text-align: center;"
                                id="calendarGrid"></div>
                            <div class="text-center mt-2"><button class="btn btn-sm btn-outline-primary"
                                    id="calToday">Hari Ini</button></div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary position-relative" type="button"
                            id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            title="Notifikasi"><i class="bi bi-bell"></i><span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                id="notificationBadge" style="display: none;">0</span></button>
                        <ul class="dropdown-menu dropdown-menu-end p-2"
                            style="width: 320px; max-height: 400px; overflow-y: auto;" id="notificationList">
                            <li class="text-center text-muted">Memuat...</li>
                        </ul>
                    </div>
                    <button id="fullscreenToggle" class="btn btn-outline-secondary" title="Fullscreen"><i
                            class="bi bi-arrows-fullscreen"></i></button>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"><i
                                class="bi bi-person-circle fs-5"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                        class="bi bi-gear me-2"></i>Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item btn-logout-trigger" href="#" style="cursor:pointer;"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="flex-grow-1 p-3 p-md-4">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- OFFCANVAS MOBILE (sama persis dengan sidebar desktop) --}}
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="offcanvasSidebar">
        <div class="offcanvas-header">
            <h5 class="fw-bold">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                @if($userRole === 'superadmin')
                    <li class="nav-item"><a href="{{ route('superadmin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="{{ route('kendaraan.index') }}"
                            class="nav-link {{ request()->routeIs('kendaraan.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-car-front me-2"></i> Kendaraan</a></li>
                    <li class="nav-item"><a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-qr-code-scan me-2"></i> Scan QR</a></li>
                    <li class="nav-item"><a href="{{ route('parkir.index') }}"
                            class="nav-link {{ request()->routeIs('parkir.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-clock-history me-2"></i> Riwayat Parkir</a></li>
                    <li class="nav-item"><a href="{{ route('laporan') }}"
                            class="nav-link {{ request()->routeIs('laporan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a></li>
                    <li class="nav-item mt-3"><span class="nav-link disabled text-muted fw-bold small">ADMIN</span></li>
                    <li class="nav-item"><a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-people-fill me-2"></i> Manajemen User</a></li>
                    <li class="nav-item"><a href="{{ route('backup.index') }}"
                            class="nav-link {{ request()->routeIs('backup.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-cloud-upload me-2"></i> Backup & Restore</a></li>
                    <li class="nav-item"><a href="{{ route('settings') }}"
                            class="nav-link {{ request()->routeIs('settings') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-gear me-2"></i> Pengaturan</a></li>
                    <li class="nav-item"><a href="{{ route('log-aktivitas.index') }}"
                            class="nav-link {{ request()->routeIs('log-aktivitas.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-journal-text me-2"></i> Log Aktivitas</a></li>
                    <li class="nav-item mt-3"><span class="nav-link disabled text-muted fw-bold small">SUPER ADMIN</span>
                    </li>
                    <li class="nav-item"><a href="{{ route('roles.index') }}"
                            class="nav-link {{ request()->routeIs('roles.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-shield-lock me-2"></i> Roles & Permissions</a></li>
                @elseif($userRole === 'admin')
                    <li class="nav-item"><a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="{{ route('kendaraan.index') }}"
                            class="nav-link {{ request()->routeIs('kendaraan.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-car-front me-2"></i> Kendaraan</a></li>
                    <li class="nav-item"><a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-qr-code-scan me-2"></i> Scan QR</a></li>
                    <li class="nav-item"><a href="{{ route('parkir.index') }}"
                            class="nav-link {{ request()->routeIs('parkir.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-clock-history me-2"></i> Riwayat Parkir</a></li>
                    <li class="nav-item"><a href="{{ route('laporan') }}"
                            class="nav-link {{ request()->routeIs('laporan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a></li>
                    <li class="nav-item mt-3"><span class="nav-link disabled text-muted fw-bold small">ADMIN</span></li>
                    <li class="nav-item"><a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-people-fill me-2"></i> Manajemen User</a></li>
                    <li class="nav-item"><a href="{{ route('backup.index') }}"
                            class="nav-link {{ request()->routeIs('backup.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-cloud-upload me-2"></i> Backup & Restore</a></li>
                    <li class="nav-item"><a href="{{ route('settings') }}"
                            class="nav-link {{ request()->routeIs('settings') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-gear me-2"></i> Pengaturan</a></li>
                    <li class="nav-item"><a href="{{ route('log-aktivitas.index') }}"
                            class="nav-link {{ request()->routeIs('log-aktivitas.*') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-journal-text me-2"></i> Log Aktivitas</a></li>
                @elseif($userRole === 'petugas')
                    <li class="nav-item mt-3"><span class="nav-link disabled text-muted fw-bold small">PETUGAS</span></li>
                    <li class="nav-item"><a href="{{ route('petugas.dashboard') }}"
                            class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="{{ route('scan') }}"
                            class="nav-link {{ request()->routeIs('scan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-qr-code-scan me-2"></i> Scan QR</a></li>
                    <li class="nav-item"><a href="{{ route('petugas.parkir') }}"
                            class="nav-link {{ request()->routeIs('petugas.parkir') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-clock-history me-2"></i> Riwayat Parkir</a></li>
                @else
                    <li class="nav-item"><a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="{{ route('mahasiswa.kendaraan') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.kendaraan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-car-front me-2"></i> Kendaraan Saya</a></li>
                    <li class="nav-item"><a href="{{ route('mahasiswa.parkir') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.parkir') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-clock-history me-2"></i> Riwayat Parkir</a></li>
                    <li class="nav-item"><a href="{{ route('mahasiswa.laporan') }}"
                            class="nav-link {{ request()->routeIs('mahasiswa.laporan') ? 'active bg-primary text-white rounded' : '' }}"><i
                                class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a></li>
                @endif
            </ul>
            <hr>
            <button class="btn btn-outline-danger w-100 btn-logout-trigger"><i
                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
        </div>
    </div>

    {{-- BOTTOM NAV MOBILE --}}
    <nav
        class="bottom-nav d-md-none fixed-bottom bg-body-tertiary border-top d-flex justify-content-around py-2 shadow">
        @if($userRole === 'superadmin')
            <a href="{{ route('superadmin.dashboard') }}"
                class="text-center text-decoration-none {{ request()->routeIs('superadmin.dashboard') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-speedometer2 fs-4"></i><small class="d-block">Dashboard</small></a>
            <a href="{{ route('kendaraan.index') }}"
                class="text-center text-decoration-none {{ request()->routeIs('kendaraan.*') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-car-front fs-4"></i><small class="d-block">Kendaraan</small></a>
            <a href="{{ route('scan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('scan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-qr-code-scan fs-4"></i><small class="d-block">Scan</small></a>
            <a href="{{ route('parkir.index') }}"
                class="text-center text-decoration-none {{ request()->routeIs('parkir.*') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-clock-history fs-4"></i><small class="d-block">Riwayat</small></a>
            <a href="{{ route('laporan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('laporan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-file-earmark-bar-graph fs-4"></i><small class="d-block">Laporan</small></a>
        @elseif($userRole === 'admin')
            <a href="{{ route('admin.dashboard') }}"
                class="text-center text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-speedometer2 fs-4"></i><small class="d-block">Dashboard</small></a>
            <a href="{{ route('kendaraan.index') }}"
                class="text-center text-decoration-none {{ request()->routeIs('kendaraan.*') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-car-front fs-4"></i><small class="d-block">Kendaraan</small></a>
            <a href="{{ route('scan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('scan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-qr-code-scan fs-4"></i><small class="d-block">Scan</small></a>
            <a href="{{ route('parkir.index') }}"
                class="text-center text-decoration-none {{ request()->routeIs('parkir.*') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-clock-history fs-4"></i><small class="d-block">Riwayat</small></a>
            <a href="{{ route('laporan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('laporan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-file-earmark-bar-graph fs-4"></i><small class="d-block">Laporan</small></a>
        @elseif($userRole === 'petugas')
            <a href="{{ route('petugas.dashboard') }}"
                class="text-center text-decoration-none {{ request()->routeIs('petugas.dashboard') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-speedometer2 fs-4"></i><small class="d-block">Dashboard</small></a>
            <a href="{{ route('scan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('scan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-qr-code-scan fs-4"></i><small class="d-block">Scan</small></a>
            <a href="{{ route('petugas.parkir') }}"
                class="text-center text-decoration-none {{ request()->routeIs('petugas.parkir') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-clock-history fs-4"></i><small class="d-block">Riwayat</small></a>
        @else
            <a href="{{ route('dashboard') }}"
                class="text-center text-decoration-none {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-speedometer2 fs-4"></i><small class="d-block">Dashboard</small></a>
            <a href="{{ route('mahasiswa.kendaraan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('mahasiswa.kendaraan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-car-front fs-4"></i><small class="d-block">Kendaraan</small></a>
            <a href="{{ route('mahasiswa.parkir') }}"
                class="text-center text-decoration-none {{ request()->routeIs('mahasiswa.parkir') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-clock-history fs-4"></i><small class="d-block">Riwayat</small></a>
            <a href="{{ route('mahasiswa.laporan') }}"
                class="text-center text-decoration-none {{ request()->routeIs('mahasiswa.laporan') ? 'text-primary' : 'text-muted' }}"><i
                    class="bi bi-file-earmark-bar-graph fs-4"></i><small class="d-block">Laporan</small></a>
        @endif
    </nav>
    <div class="d-md-none pb-5"></div>

    <!-- JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    {{-- DARK MODE --}}
    <script>
        (function () {
            const html = document.documentElement;
            const toggleBtn = document.getElementById('darkModeToggle');
            const icon = toggleBtn.querySelector('i');
            if (localStorage.getItem('theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                icon.classList.replace('bi-moon-stars', 'bi-sun');
            }
            toggleBtn.addEventListener('click', () => {
                const current = html.getAttribute('data-bs-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-bs-theme', next);
                localStorage.setItem('theme', next);
                if (next === 'dark') {
                    icon.classList.replace('bi-moon-stars', 'bi-sun');
                } else {
                    icon.classList.replace('bi-sun', 'bi-moon-stars');
                }
            });
        })();
    </script>

    {{-- JAM DIGITAL --}}
    <script>
        (function () {
            const clock = document.getElementById('liveClock');
            if (!clock) return;

            function tick() {
                const now = new Date();
                const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                clock.querySelector('span').textContent = time;
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>

    {{-- KALENDER MINI --}}
    <script>
        (function () {
            const calMonthYear = document.getElementById('calMonthYear');
            const calGrid = document.getElementById('calendarGrid');
            const calPrev = document.getElementById('calPrev');
            const calNext = document.getElementById('calNext');
            const calTodayBtn = document.getElementById('calToday');
            let currentDate = new Date();

            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                calMonthYear.textContent = new Date(year, month).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                let html = '';
                const hari = ['Mi', 'Se', 'Se', 'Ra', 'Ka', 'Ju', 'Sa'];
                hari.forEach(h => { html += `<div class="fw-bold small text-muted">${h}</div>`; });
                for (let i = 0; i < firstDay; i++) { html += '<div></div>'; }
                const today = new Date();
                const isCurrentMonth = (today.getMonth() === month && today.getFullYear() === year);
                for (let d = 1; d <= daysInMonth; d++) {
                    const isToday = isCurrentMonth && d === today.getDate();
                    const className = isToday ? 'bg-primary text-white rounded-circle' : '';
                    html += `<div class="py-1 ${className}" style="cursor: default;">${d}</div>`;
                }
                calGrid.innerHTML = html;
            }
            calPrev.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
            calNext.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
            calTodayBtn.addEventListener('click', (e) => { e.stopPropagation(); currentDate = new Date(); renderCalendar(); });
            renderCalendar();
        })();
    </script>

    {{-- FULLSCREEN TOGGLE --}}
    <script>
        (function () {
            const toggleBtn = document.getElementById('fullscreenToggle');
            const icon = toggleBtn.querySelector('i');

            function enterFullscreen() { document.documentElement.requestFullscreen().catch(err => console.warn(err)); }

            function updateIcon() {
                if (document.fullscreenElement) {
                    icon.classList.replace('bi-arrows-fullscreen', 'bi-fullscreen-exit');
                } else {
                    icon.classList.replace('bi-fullscreen-exit', 'bi-arrows-fullscreen');
                }
            }
            toggleBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) enterFullscreen();
                else document.exitFullscreen();
            });
            document.addEventListener('fullscreenchange', updateIcon);
            updateIcon();
        })();
    </script>

    {{-- SIDEBAR TOGGLE --}}
    <script>
        (function () {
            const sidebar = document.getElementById('sidebarDesktop');
            const toggleBtn = document.getElementById('sidebarToggle');
            const icon = toggleBtn.querySelector('i');
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                icon.classList.replace('bi-list', 'bi-chevron-right');
                toggleBtn.classList.add('rotated');
            }
            toggleBtn.addEventListener('click', () => {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                if (isCollapsed) {
                    icon.classList.replace('bi-list', 'bi-chevron-right');
                    toggleBtn.classList.add('rotated');
                } else {
                    icon.classList.replace('bi-chevron-right', 'bi-list');
                    toggleBtn.classList.remove('rotated');
                }
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
        })();
    </script>

    {{-- NOTIFIKASI --}}
    <script>
        (function () {
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            const dropdownBtn = document.getElementById('notificationDropdown');

            // Helper: waktu relatif
            function timeAgo(dateString) {
                const now = new Date();
                const then = new Date(dateString);
                const diff = Math.floor((now - then) / 1000); // detik
                if (diff < 60) return 'Baru saja';
                const minutes = Math.floor(diff / 60);
                if (minutes < 60) return minutes + ' mnt lalu';
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return hours + ' jam lalu';
                const days = Math.floor(hours / 24);
                return days + ' hari lalu';
            }

            // Animasi badge berdenyut
            function pulseBadge() {
                if (badge.style.display === 'inline-block') {
                    badge.classList.add('animate__animated', 'animate__heartBeat');
                    setTimeout(() => badge.classList.remove('animate__animated', 'animate__heartBeat'), 1000);
                }
            }

            async function loadNotifications() {
                try {
                    // Tampilkan loading
                    list.innerHTML = `<li class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <small class="ms-2 text-muted">Memuat...</small>
                </li>`;

                    const response = await fetch('{{ route("notifications.data") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('HTTP ' + response.status);

                    const data = await response.json();

                    // Update badge
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'inline-block';
                        pulseBadge();
                    } else {
                        badge.style.display = 'none';
                    }

                    // Render list
                    if (!data.notifications || data.notifications.length === 0) {
                        list.innerHTML = `<li class="text-center py-4 text-muted">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                        <small>Tidak ada notifikasi</small>
                    </li>`;
                        return;
                    }

                    let html = '';
                    data.notifications.forEach(notif => {
                        const message = notif.data?.message ?? 'Notifikasi';
                        const time = timeAgo(notif.created_at);
                        const type = notif.type || 'info';
                        const icon = notif.icon || 'bi-bell';

                        html += `
                    <li class="dropdown-item px-3 py-2 border-start border-3 border-${type} ${notif.read_at ? '' : 'bg-light'}">
                        <div class="d-flex align-items-start">
                            <span class="badge bg-${type} rounded-circle p-2 me-2">
                                <i class="bi ${icon}"></i>
                            </span>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-semibold text-truncate">${message}</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">${time}</small>
                                    ${!notif.read_at ? `
                                    <button class="btn btn-sm btn-outline-secondary mark-read" data-id="${notif.id}" title="Tandai sudah dibaca">
                                        <i class="bi bi-check2"></i>
                                    </button>` : `<small class="text-success"><i class="bi bi-check-all"></i></small>`}
                                </div>
                            </div>
                        </div>
                    </li>`;
                    });

                    // Tambah tombol "Tandai semua sudah dibaca" jika ada notif yang belum dibaca
                    if (data.unread_count > 0) {
                        html += `<li class="text-center py-2">
                        <button class="btn btn-sm btn-link text-decoration-none mark-all-read">Tandai semua sudah dibaca</button>
                    </li>`;
                    }

                    list.innerHTML = html;

                    // Event listeners
                    list.querySelectorAll('.mark-read').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            e.stopPropagation();
                            const id = btn.dataset.id;
                            try {
                                const res = await fetch(`/notifications/${id}/read`, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const result = await res.json();
                                if (result.success) loadNotifications();
                            } catch (err) {
                                console.error('Gagal menandai notifikasi:', err);
                            }
                        });
                    });

                    // Tombol tandai semua
                    const markAllBtn = list.querySelector('.mark-all-read');
                    if (markAllBtn) {
                        markAllBtn.addEventListener('click', async () => {
                            try {
                                const unreadItems = list.querySelectorAll('.mark-read');
                                for (let btn of unreadItems) {
                                    const id = btn.dataset.id;
                                    await fetch(`/notifications/${id}/read`, {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                }
                                loadNotifications();
                            } catch (err) {
                                console.error('Gagal menandai semua:', err);
                            }
                        });
                    }

                } catch (err) {
                    console.error('Notifikasi error:', err);
                    list.innerHTML = `<li class="text-center py-3 text-muted">
                    <i class="bi bi-wifi-off fs-3 d-block mb-2"></i>
                    <small>Gagal memuat notifikasi</small>
                </li>`;
                }
            }

            // Muat saat tombol lonceng diklik
            dropdownBtn.addEventListener('click', loadNotifications);

            // Polling setiap 30 detik
            setInterval(loadNotifications, 30000);

            // Muat pertama kali setelah halaman siap
            document.addEventListener('DOMContentLoaded', loadNotifications);
        })();
    </script>

    {{-- LOGOUT SweetAlert2 --}}
    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-logout-trigger')) {
                e.preventDefault();
                const logoutForm = document.createElement('form');
                logoutForm.method = 'POST';
                logoutForm.action = '{{ route('logout') }}';
                logoutForm.innerHTML = '{{ csrf_field() }}';
                document.body.appendChild(logoutForm);
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    html: '<p class="mb-4">Anda yakin ingin keluar?<br><small class="text-muted">Semua sesi akan diakhiri.</small></p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Logout',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' },
                    buttonsStyling: false
                }).then((result) => { if (result.isConfirmed) logoutForm.submit(); });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>