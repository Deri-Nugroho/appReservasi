<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$action = $_REQUEST['action'] ?? '';

/**
 * Fungsi cek bentrok jadwal untuk room tertentu.
 * Bentrok terjadi jika ada booking lain (status bukan 'dibatalkan')
 * yang rentang waktunya overlap dengan rentang waktu baru.
 */
function isConflict($pdo, $room_id, $mulai, $selesai, $excludeId = null) {
    $sql = "SELECT COUNT(*) c FROM bookings
            WHERE room_id = :room_id
              AND status != 'dibatalkan'
              AND (:mulai < tanggal_selesai AND :selesai > tanggal_mulai)";
    $params = [
        ':room_id' => $room_id,
        ':mulai' => $mulai,
        ':selesai' => $selesai,
    ];

    if ($excludeId) {
        $sql .= " AND id != :excludeId";
        $params[':excludeId'] = $excludeId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch()['c'] > 0;
}

switch ($action) {

    case 'read':
        $status = $_GET['status'] ?? '';
        $sql = "SELECT b.*, r.nama_ruangan
                FROM bookings b
                JOIN rooms r ON r.id = b.room_id";
        $params = [];
        if ($status !== '') {
            $sql .= " WHERE b.status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY b.tanggal_mulai DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'check_conflict':
        $room_id = (int) ($_POST['room_id'] ?? 0);
        $mulai = str_replace('T', ' ', $_POST['tanggal_mulai'] ?? '') . ':00';
        $selesai = str_replace('T', ' ', $_POST['tanggal_selesai'] ?? '') . ':00';
        $excludeId = !empty($_POST['booking_id']) ? (int) $_POST['booking_id'] : null;

        $conflict = isConflict($pdo, $room_id, $mulai, $selesai, $excludeId);
        echo json_encode(['success' => true, 'conflict' => $conflict]);
        break;

    case 'create':
        $room_id = (int) ($_POST['room_id'] ?? 0);
        $nama = trim($_POST['nama_pemesan'] ?? '');
        $email = trim($_POST['email_pemesan'] ?? '');
        $telp = trim($_POST['no_telepon'] ?? '');
        $keperluan = trim($_POST['keperluan'] ?? '');
        $mulai = str_replace('T', ' ', $_POST['tanggal_mulai'] ?? '') . ':00';
        $selesai = str_replace('T', ' ', $_POST['tanggal_selesai'] ?? '') . ':00';
        $status = $_POST['status'] ?? 'pending';
        $catatan = trim($_POST['catatan'] ?? '');

        if ($room_id <= 0 || $nama === '') {
            echo json_encode(['success' => false, 'message' => 'Ruangan dan nama pemesan wajib diisi.']);
            break;
        }

        if (isConflict($pdo, $room_id, $mulai, $selesai)) {
            echo json_encode(['success' => false, 'message' => 'Jadwal bentrok dengan reservasi lain.']);
            break;
        }

        $stmt = $pdo->prepare("INSERT INTO bookings
            (room_id, nama_pemesan, email_pemesan, no_telepon, keperluan, tanggal_mulai, tanggal_selesai, status, catatan)
            VALUES (:room_id, :nama, :email, :telp, :keperluan, :mulai, :selesai, :status, :catatan)");
        $stmt->execute([
            ':room_id' => $room_id,
            ':nama' => $nama,
            ':email' => $email,
            ':telp' => $telp,
            ':keperluan' => $keperluan,
            ':mulai' => $mulai,
            ':selesai' => $selesai,
            ':status' => $status,
            ':catatan' => $catatan,
        ]);

        echo json_encode(['success' => true, 'message' => 'Reservasi berhasil ditambahkan.']);
        break;

    case 'update':
        $id = (int) ($_POST['id'] ?? 0);
        $room_id = (int) ($_POST['room_id'] ?? 0);
        $nama = trim($_POST['nama_pemesan'] ?? '');
        $email = trim($_POST['email_pemesan'] ?? '');
        $telp = trim($_POST['no_telepon'] ?? '');
        $keperluan = trim($_POST['keperluan'] ?? '');
        $mulai = str_replace('T', ' ', $_POST['tanggal_mulai'] ?? '') . ':00';
        $selesai = str_replace('T', ' ', $_POST['tanggal_selesai'] ?? '') . ':00';
        $status = $_POST['status'] ?? 'pending';
        $catatan = trim($_POST['catatan'] ?? '');

        if ($id <= 0 || $room_id <= 0 || $nama === '') {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            break;
        }

        if (isConflict($pdo, $room_id, $mulai, $selesai, $id)) {
            echo json_encode(['success' => false, 'message' => 'Jadwal bentrok dengan reservasi lain.']);
            break;
        }

        $stmt = $pdo->prepare("UPDATE bookings SET
            room_id = :room_id,
            nama_pemesan = :nama,
            email_pemesan = :email,
            no_telepon = :telp,
            keperluan = :keperluan,
            tanggal_mulai = :mulai,
            tanggal_selesai = :selesai,
            status = :status,
            catatan = :catatan
            WHERE id = :id");
        $stmt->execute([
            ':room_id' => $room_id,
            ':nama' => $nama,
            ':email' => $email,
            ':telp' => $telp,
            ':keperluan' => $keperluan,
            ':mulai' => $mulai,
            ':selesai' => $selesai,
            ':status' => $status,
            ':catatan' => $catatan,
            ':id' => $id,
        ]);

        echo json_encode(['success' => true, 'message' => 'Reservasi berhasil diperbarui.']);
        break;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            break;
        }

        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Reservasi berhasil dihapus.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali.']);
}
