<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-calendar-check"></i> Data Reservasi</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal" onclick="resetForm()">
        <i class="bi bi-plus-circle"></i> Tambah Reservasi
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-4">
                <select class="form-select" id="filterStatus" onchange="loadBookings()">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="dikonfirmasi">Dikonfirmasi</option>
                    <option value="dibatalkan">Dibatalkan</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Pemesan</th>
                    <th>Ruangan</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody"></tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Booking -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bookingForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalTitle">Tambah Reservasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="booking_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Ruangan</label>
                        <select class="form-select" id="room_id" name="room_id" required></select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pemesan</label>
                            <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_pemesan" name="email_pemesan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keperluan</label>
                        <input type="text" class="form-control" id="keperluan" name="keperluan">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal & Jam Mulai</label>
                            <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal & Jam Selesai</label>
                            <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="dikonfirmasi">Dikonfirmasi</option>
                            <option value="dibatalkan">Dibatalkan</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2"></textarea>
                    </div>

                    <div id="conflictWarning" class="alert alert-danger d-none">
                        <i class="bi bi-exclamation-triangle"></i>
                        Ruangan sudah dibooking pada rentang waktu tersebut!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanBooking">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

function badgeStatus(status) {
    const label = status.charAt(0).toUpperCase() + status.slice(1);
    return `<span class="badge badge-status-${status}">${label}</span>`;
}

function formatTanggal(dt) {
    const d = new Date(dt);
    return d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// Load dropdown ruangan
function loadRoomOptions(selected = null) {
    $.ajax({
        url: 'ajax/rooms_ajax.php',
        method: 'GET',
        data: { action: 'read' },
        dataType: 'json',
        success: function (res) {
            let options = '<option value="">-- Pilih Ruangan --</option>';
            res.data.forEach(r => {
                const sel = (selected == r.id) ? 'selected' : '';
                options += `<option value="${r.id}" ${sel}>${r.nama_ruangan} (${r.tipe})</option>`;
            });
            $('#room_id').html(options);
        }
    });
}

function loadBookings() {
    $.ajax({
        url: 'ajax/bookings_ajax.php',
        method: 'GET',
        data: { action: 'read', status: $('#filterStatus').val() },
        dataType: 'json',
        success: function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = `<tr><td colspan="7" class="text-center text-muted py-3">Belum ada data reservasi</td></tr>`;
            } else {
                res.data.forEach((b, i) => {
                    rows += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${b.nama_pemesan}</td>
                        <td>${b.nama_ruangan}</td>
                        <td>${formatTanggal(b.tanggal_mulai)}</td>
                        <td>${formatTanggal(b.tanggal_selesai)}</td>
                        <td>${badgeStatus(b.status)}</td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-warning" onclick='editBooking(${JSON.stringify(b)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBooking(${b.id})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#bookingsTableBody').html(rows);
        },
        error: function () {
            alert('Gagal memuat data reservasi.');
        }
    });
}

function resetForm() {
    $('#bookingForm')[0].reset();
    $('#booking_id').val('');
    $('#conflictWarning').addClass('d-none');
    $('#bookingModalTitle').text('Tambah Reservasi');
    loadRoomOptions();
}

function toDatetimeLocal(value) {
    // format dari MySQL "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DDTHH:MM"
    return value.replace(' ', 'T').substring(0, 16);
}

function editBooking(b) {
    loadRoomOptions(b.room_id);
    $('#booking_id').val(b.id);
    $('#nama_pemesan').val(b.nama_pemesan);
    $('#no_telepon').val(b.no_telepon);
    $('#email_pemesan').val(b.email_pemesan);
    $('#keperluan').val(b.keperluan);
    $('#tanggal_mulai').val(toDatetimeLocal(b.tanggal_mulai));
    $('#tanggal_selesai').val(toDatetimeLocal(b.tanggal_selesai));
    $('#status').val(b.status);
    $('#catatan').val(b.catatan);
    $('#conflictWarning').addClass('d-none');
    $('#bookingModalTitle').text('Edit Reservasi');
    bookingModal.show();
}

function deleteBooking(id) {
    if (!confirm('Yakin ingin menghapus reservasi ini?')) return;
    $.ajax({
        url: 'ajax/bookings_ajax.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                loadBookings();
            } else {
                alert(res.message || 'Gagal menghapus data.');
            }
        }
    });
}

// Cek bentrok jadwal secara real-time (AJAX) sebelum submit
function cekBentrok(callback) {
    const room_id = $('#room_id').val();
    const mulai = $('#tanggal_mulai').val();
    const selesai = $('#tanggal_selesai').val();
    const booking_id = $('#booking_id').val();

    if (!room_id || !mulai || !selesai) {
        callback(false);
        return;
    }

    $.ajax({
        url: 'ajax/bookings_ajax.php',
        method: 'POST',
        data: {
            action: 'check_conflict',
            room_id: room_id,
            tanggal_mulai: mulai,
            tanggal_selesai: selesai,
            booking_id: booking_id
        },
        dataType: 'json',
        success: function (res) {
            callback(res.conflict);
        }
    });
}

$('#bookingForm').on('submit', function (e) {
    e.preventDefault();

    if ($('#tanggal_selesai').val() <= $('#tanggal_mulai').val()) {
        alert('Tanggal selesai harus setelah tanggal mulai.');
        return;
    }

    cekBentrok(function (conflict) {
        if (conflict) {
            $('#conflictWarning').removeClass('d-none');
            return;
        }
        $('#conflictWarning').addClass('d-none');

        const id = $('#booking_id').val();
        const action = id ? 'update' : 'create';

        $.ajax({
            url: 'ajax/bookings_ajax.php',
            method: 'POST',
            data: $('#bookingForm').serialize() + '&action=' + action,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    bookingModal.hide();
                    loadBookings();
                } else {
                    alert(res.message || 'Gagal menyimpan data.');
                }
            },
            error: function () {
                alert('Terjadi kesalahan pada server.');
            }
        });
    });
});

$(document).ready(function () {
    loadRoomOptions();
    loadBookings();
});
</script>
