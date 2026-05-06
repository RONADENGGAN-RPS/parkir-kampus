@extends('layouts.app')
@section('title', 'Scan QR')
@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            {{-- Card Scanner --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary bg-gradient text-white text-center py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code Kendaraan
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- Area Kamera --}}
                    <div id="reader"
                        style="width:100%; border-radius:12px; overflow:hidden; background:#000; min-height:300px;"></div>

                    {{-- Status Scan --}}
                    <div id="scanStatus" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <p class="text-muted small mb-0">Memproses data...</p>
                    </div>

                    {{-- Hasil Scan Terakhir --}}
                    <div id="lastResult" class="alert alert-success mt-3 d-none" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span id="lastResultText"></span>
                    </div>

                    {{-- Tombol Kontrol --}}
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button id="toggleScanBtn" class="btn btn-primary rounded-pill px-4" onclick="toggleScan()">
                            <i class="bi bi-camera-video me-1"></i> <span id="btnText">Mulai Scan</span>
                        </button>
                        <button id="switchCameraBtn" class="btn btn-outline-secondary rounded-pill px-3 d-none"
                            onclick="switchCamera()" title="Ganti Kamera">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                    <p class="text-muted small mt-2 text-center">Arahkan kamera ke QR Code kendaraan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Scan Terbaru --}}
    <div class="row justify-content-center mt-4">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-clock-history me-2"></i> Aktivitas Scan Terbaru
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="refreshRecentScans()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="card-body p-0" style="max-height:220px; overflow-y:auto;" id="recentScanContainer">
                    <ul class="list-group list-group-flush" id="recentScans">
                        @forelse(\App\Models\Parkir::with('kendaraan')->latest()->limit(5)->get() as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $p->kendaraan->plat_nomor ?? 'N/A' }}</strong>
                                    <span class="badge bg-{{ $p->status === 'active' ? 'warning' : 'success' }} ms-2">
                                        {{ $p->status === 'active' ? 'Check-in' : 'Check-out' }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $p->check_in->format('H:i') }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Belum ada aktivitas scan</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let html5QrCode = null;
        let scanning = false;
        let currentFacingMode = "environment"; // default kamera belakang
        let lastDecodedText = null;

        const toggleBtn = document.getElementById('toggleScanBtn');
        const btnText = document.getElementById('btnText');
        const scanStatus = document.getElementById('scanStatus');
        const switchBtn = document.getElementById('switchCameraBtn');
        const readerDiv = document.getElementById('reader');
        const lastResult = document.getElementById('lastResult');
        const lastResultText = document.getElementById('lastResultText');

        // Pastikan Html5Qrcode tersedia
        if (typeof Html5Qrcode === 'undefined') {
            readerDiv.innerHTML = '<div class="alert alert-warning m-3">Library QR Code tidak tersedia. Periksa koneksi internet.</div>';
        }

        function getQrCodeInstance() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            return html5QrCode;
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Hindari pemrosesan ganda untuk QR yang sama dalam 2 detik
            if (lastDecodedText === decodedText) {
                return;
            }
            lastDecodedText = decodedText;

            // Hentikan scan sementara
            const qr = getQrCodeInstance();
            qr.stop().then(() => {
                prosesData(decodedText);
            }).catch(() => {
                // Jika gagal stop, langsung proses
                prosesData(decodedText);
            });
        }

        function prosesData(qrData) {
            // Tampilkan loading
            scanStatus.classList.remove('d-none');
            lastResult.classList.add('d-none');

            fetch('{{ route('web.scan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData })
            })
                .then(res => res.json())
                .then(data => {
                    scanStatus.classList.add('d-none');
                    if (data.success) {
                        // Tampilkan hasil
                        lastResult.classList.remove('d-none');
                        lastResult.className = 'alert alert-success mt-3';
                        lastResultText.textContent = data.message;

                        Swal.fire({
                            icon: 'success',
                            title: data.action === 'checkin' ? 'Check-in Berhasil ✅' : 'Check-out Berhasil ✅',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Refresh riwayat scan
                        refreshRecentScans();
                    } else {
                        lastResult.classList.remove('d-none');
                        lastResult.className = 'alert alert-danger mt-3';
                        lastResultText.textContent = data.message;

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                    // Reset lastDecodedText setelah 2 detik
                    setTimeout(() => {
                        lastDecodedText = null;
                    }, 2000);

                    // Lanjutkan scan setelah 2.5 detik
                    setTimeout(() => {
                        startScan();
                    }, 2500);
                })
                .catch(err => {
                    scanStatus.classList.add('d-none');
                    lastResult.classList.remove('d-none');
                    lastResult.className = 'alert alert-danger mt-3';
                    lastResultText.textContent = 'Gagal terhubung ke server';

                    Swal.fire('Error', 'Gagal terhubung ke server', 'error');
                    setTimeout(() => startScan(), 2500);
                });
        }

        function onScanFailure(error) {
            // Abaikan error scanning rutin
        }

        function startScan() {
            const qr = getQrCodeInstance();
            qr.start(
                { facingMode: currentFacingMode },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                scanning = true;
                btnText.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Hentikan';
                toggleBtn.classList.remove('btn-primary');
                toggleBtn.classList.add('btn-danger');
                switchBtn.classList.remove('d-none');
            }).catch(err => {
                console.error('Kamera error:', err);
                Swal.fire({
                    icon: 'warning',
                    title: 'Kamera Tidak Tersedia',
                    text: 'Tidak dapat mengakses kamera. Pastikan Anda menggunakan HTTPS atau localhost, dan izinkan akses kamera.',
                    confirmButtonText: 'Coba Lagi'
                }).then(() => {
                    startScan();
                });
            });
        }

        function stopScan() {
            const qr = getQrCodeInstance();
            if (qr) {
                qr.stop().then(() => {
                    scanning = false;
                    btnText.innerHTML = '<i class="bi bi-camera-video me-1"></i> Mulai Scan';
                    toggleBtn.classList.remove('btn-danger');
                    toggleBtn.classList.add('btn-primary');
                    switchBtn.classList.add('d-none');
                }).catch(err => console.error('Gagal menghentikan kamera:', err));
            }
        }

        function toggleScan() {
            if (scanning) {
                stopScan();
            } else {
                startScan();
            }
        }

        function switchCamera() {
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
            stopScan();
            setTimeout(() => {
                startScan();
            }, 500);
        }

        function refreshRecentScans() {
            // Reload halaman untuk mendapatkan data terbaru
            // Atau bisa gunakan AJAX jika ingin lebih dinamis
            location.reload();
        }
    </script>

    <style>
        /* Animasi pulse untuk hasil scan */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .alert {
            animation: pulse 0.5s ease-in-out;
        }

        /* Loading spinner custom */
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
@endsection