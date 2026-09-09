<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-door-open"></i> Data Ruangan</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roomModal" onclick="resetForm()">
        <i class="bi bi-plus-circle"></i> Tambah Ruangan
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle" id="roomsTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Ruangan</th>
                    <th>Tipe</th>
                    <th>Kapasitas</th>
                    <th>Harga/Jam</th>
                    <th>Status</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody id="roomsTableBody">
                <!-- diisi via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Ruangan -->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="roomForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomModalTitle">Tambah Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="room_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" id="tipe" name="tipe" required>
                            <option value="Meeting Room">Meeting Room</option>
                            <option value="Kamar Hotel">Kamar Hotel</option>
                            <option value="Aula">Aula</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Kapasitas</label>
                            <input type="number" min="1" class="form-control" id="kapasitas" name="kapasitas" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga / Jam (Rp)</label>
                            <input type="number" min="0" class="form-control" id="harga_per_jam" name="harga_per_jam" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea class="form-control" id="fasilitas" name="fasilitas" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="perbaikan">Perbaikan</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
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

<?php require_once 'includes/footer.php'; ?>

<script>
const roomModal = new bootstrap.Modal(document.getElementById('roomModal'));

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function badgeStatus(status) {
    const label = status.charAt(0).toUpperCase() + status.slice(1);
    return `<span class="badge badge-status-${status}">${label}</span>`;
}

function loadRooms() {
    $.ajax({
        url: 'ajax/rooms_ajax.php',
        method: 'GET',
        data: { action: 'read' },
        dataType: 'json',
        success: function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = `<tr><td colspan="7" class="text-center text-muted py-3">Belum ada data ruangan</td></tr>`;
            } else {
                res.data.forEach((r, i) => {
                    rows += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${r.nama_ruangan}</td>
                        <td>${r.tipe}</td>
                        <td>${r.kapasitas} orang</td>
                        <td>${formatRupiah(r.harga_per_jam)}</td>
                        <td>${badgeStatus(r.status)}</td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-warning" onclick='editRoom(${JSON.stringify(r)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(${r.id})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#roomsTableBody').html(rows);
        },
        error: function () {
            alert('Gagal memuat data ruangan.');
        }
    });
}

function resetForm() {
    $('#roomForm')[0].reset();
    $('#room_id').val('');
    $('#roomModalTitle').text('Tambah Ruangan');
}

function editRoom(room) {
    $('#room_id').val(room.id);
    $('#nama_ruangan').val(room.nama_ruangan);
    $('#tipe').val(room.tipe);
    $('#kapasitas').val(room.kapasitas);
    $('#harga_per_jam').val(room.harga_per_jam);
    $('#fasilitas').val(room.fasilitas);
    $('#status').val(room.status);
    $('#roomModalTitle').text('Edit Ruangan');
    roomModal.show();
}

function deleteRoom(id) {
    if (!confirm('Yakin ingin menghapus ruangan ini? Semua data booking terkait juga akan terhapus.')) return;
    $.ajax({
        url: 'ajax/rooms_ajax.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                loadRooms();
            } else {
                alert(res.message || 'Gagal menghapus data.');
            }
        }
    });
}

$('#roomForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#room_id').val();
    const action = id ? 'update' : 'create';

    $.ajax({
        url: 'ajax/rooms_ajax.php',
        method: 'POST',
        data: $(this).serialize() + '&action=' + action,
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                roomModal.hide();
                loadRooms();
            } else {
                alert(res.message || 'Gagal menyimpan data.');
            }
        },
        error: function () {
            alert('Terjadi kesalahan pada server.');
        }
    });
});

$(document).ready(function () {
    loadRooms();
});
</script>
