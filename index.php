<?php require_once 'config/database.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Statistik ringkas
$totalRooms = $pdo->query("SELECT COUNT(*) c FROM rooms")->fetch()['c'];
$totalTersedia = $pdo->query("SELECT COUNT(*) c FROM rooms WHERE status='tersedia'")->fetch()['c'];
$totalBookingHariIni = $pdo->query("SELECT COUNT(*) c FROM bookings WHERE DATE(tanggal_mulai) = CURDATE()")->fetch()['c'];
$totalPending = $pdo->query("SELECT COUNT(*) c FROM bookings WHERE status='pending'")->fetch()['c'];

$bookingTerbaru = $pdo->query("
    SELECT b.*, r.nama_ruangan
    FROM bookings b
    JOIN rooms r ON r.id = b.room_id
    ORDER BY b.created_at DESC
    LIMIT 5
")->fetchAll();
?>

<h3 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Total Ruangan</div>
            <div class="fs-3 fw-bold"><?php echo $totalRooms; ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Ruangan Tersedia</div>
            <div class="fs-3 fw-bold text-success"><?php echo $totalTersedia; ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Booking Hari Ini</div>
            <div class="fs-3 fw-bold text-primary"><?php echo $totalBookingHariIni; ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Menunggu Konfirmasi</div>
            <div class="fs-3 fw-bold text-warning"><?php echo $totalPending; ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clock-history"></i> Reservasi Terbaru
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pemesan</th>
                    <th>Ruangan</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookingTerbaru)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data reservasi</td></tr>
                <?php else: foreach ($bookingTerbaru as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['nama_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($b['nama_ruangan']); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($b['tanggal_mulai'])); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($b['tanggal_selesai'])); ?></td>
                        <td><span class="badge badge-status-<?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
