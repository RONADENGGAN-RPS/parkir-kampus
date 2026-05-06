{{-- resources/views/partials/qr-modal.blade.php --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 rounded-top-4">
                <h5 class="mb-0 fw-bold">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div id="qrCodeContainer" class="d-flex justify-content-center mb-3">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <p class="fw-bold mb-1" id="qrPlatText"></p>
                <small class="text-muted">Scan untuk check‑in / check‑out</small>
            </div>
            <div class="modal-footer bg-light border-0 rounded-bottom-4 justify-content-center">
                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="unduhQR()">
                    <i class="bi bi-download me-1"></i> Unduh
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
    let currentQrData = null;
    let currentPlat = null;

    function showQR(id, plat) {
        currentPlat = plat;
        $('#qrPlatText').text(plat);
        $('#qrCodeContainer').html('<div class="spinner-border text-primary" role="status"></div>');
        $.get('/kendaraan/' + id + '/qr', function(res) {
            if (res.success) {
                currentQrData = res.qr_data;
                QRCode.toCanvas(document.createElement('canvas'), res.qr_data, { width: 220 }, function(err, canvas) {
                    if (err) {
                        $('#qrCodeContainer').html('<div class="alert alert-danger">Gagal generate QR</div>');
                    } else {
                        $('#qrCodeContainer').html(canvas);
                    }
                });
                $('#qrModal').modal('show');
            } else {
                $('#qrCodeContainer').html('<div class="alert alert-danger">QR tidak ditemukan</div>');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', 'Gagal mengambil QR Code. ' + (xhr.responseJSON?.message || ''), 'error');
        });
    }

    function unduhQR() {
        if (!currentQrData) {
            Swal.fire('Oops', 'QR belum dimuat', 'warning');
            return;
        }
        const canvas = document.createElement('canvas');
        QRCode.toCanvas(canvas, currentQrData, { width: 500 }, function(err) {
            if (err) {
                Swal.fire('Error', 'Gagal generate QR untuk unduh', 'error');
                return;
            }
            const link = document.createElement('a');
            link.download = 'QR_' + currentPlat.replace(/\s/g, '_') + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }

    $('#qrModal').on('hidden.bs.modal', function() {
        currentQrData = null;
        currentPlat = null;
    });
</script>