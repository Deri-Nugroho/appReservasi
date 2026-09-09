<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Reservasi Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .navbar-brand { font-weight: 600; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .badge-status-tersedia { background-color: #198754; }
        .badge-status-perbaikan { background-color: #fd7e14; }
        .badge-status-nonaktif { background-color: #6c757d; }
        .badge-status-pending { background-color: #ffc107; color:#000; }
        .badge-status-dikonfirmasi { background-color: #198754; }
        .badge-status-dibatalkan { background-color: #dc3545; }
        .badge-status-selesai { background-color: #6c757d; }
        .table-actions button { margin-right: 4px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-building"></i> Reservasi Ruangan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="rooms.php"><i class="bi bi-door-open"></i> Data Ruangan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookings.php"><i class="bi bi-calendar-check"></i> Reservasi</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container pb-5">
