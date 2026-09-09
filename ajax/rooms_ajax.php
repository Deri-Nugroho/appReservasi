<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'read':
        $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'create':
        $nama = trim($_POST['nama_ruangan'] ?? '');
        $tipe = $_POST['tipe'] ?? '';
        $kapasitas = (int) ($_POST['kapasitas'] ?? 0);
        $harga = (float) ($_POST['harga_per_jam'] ?? 0);
        $fasilitas = trim($_POST['fasilitas'] ?? '');
        $status = $_POST['status'] ?? 'tersedia';

        if ($nama === '' || $kapasitas <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nama ruangan dan kapasitas wajib diisi.']);
            break;
        }

        $stmt = $pdo->prepare("INSERT INTO rooms (nama_ruangan, tipe, kapasitas, harga_per_jam, fasilitas, status)
                                VALUES (:nama, :tipe, :kapasitas, :harga, :fasilitas, :status)");
        $stmt->execute([
            ':nama' => $nama,
            ':tipe' => $tipe,
            ':kapasitas' => $kapasitas,
            ':harga' => $harga,
            ':fasilitas' => $fasilitas,
            ':status' => $status,
        ]);

        echo json_encode(['success' => true, 'message' => 'Ruangan berhasil ditambahkan.']);
        break;

    case 'update':
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama_ruangan'] ?? '');
        $tipe = $_POST['tipe'] ?? '';
        $kapasitas = (int) ($_POST['kapasitas'] ?? 0);
        $harga = (float) ($_POST['harga_per_jam'] ?? 0);
        $fasilitas = trim($_POST['fasilitas'] ?? '');
        $status = $_POST['status'] ?? 'tersedia';

        if ($id <= 0 || $nama === '' || $kapasitas <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            break;
        }

        $stmt = $pdo->prepare("UPDATE rooms SET
                                nama_ruangan = :nama,
                                tipe = :tipe,
                                kapasitas = :kapasitas,
                                harga_per_jam = :harga,
                                fasilitas = :fasilitas,
                                status = :status
                                WHERE id = :id");
        $stmt->execute([
            ':nama' => $nama,
            ':tipe' => $tipe,
            ':kapasitas' => $kapasitas,
            ':harga' => $harga,
            ':fasilitas' => $fasilitas,
            ':status' => $status,
            ':id' => $id,
        ]);

        echo json_encode(['success' => true, 'message' => 'Ruangan berhasil diperbarui.']);
        break;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            break;
        }

        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Ruangan berhasil dihapus.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali.']);
}
