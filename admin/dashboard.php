<?php
require_once '../config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin - Yayasan</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="edit_page.php?page=tentang">Edit Tentang</a>
            <a href="edit_page.php?page=yayasan">Edit Yayasan</a>
            <a href="edit_struktur.php">Edit Struktur</a>
            <a href="manage_berita.php">Manage Berita</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>
        <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
        <p>Gunakan menu di atas untuk mengelola konten website yayasan.</p>
    </div>
</body>
</html>
