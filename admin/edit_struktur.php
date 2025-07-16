<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];

    // Handle file upload if exists
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Format gambar tidak didukung. Gunakan JPG atau PNG.';
        } else {
            $uploadDir = '../assets/images/';
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update image path and description in DB
                $stmt = $pdo->prepare("UPDATE struktur SET image_path = ?, description = ? WHERE id = 1");
                if ($stmt->execute(['assets/images/' . $fileName, $description])) {
                    $success = 'Struktur organisasi berhasil diperbarui.';
                } else {
                    $error = 'Gagal menyimpan data ke database.';
                }
            } else {
                $error = 'Gagal mengunggah gambar.';
            }
        }
    } else {
        // Update only description if no image uploaded
        $stmt = $pdo->prepare("UPDATE struktur SET description = ? WHERE id = 1");
        if ($stmt->execute([$description])) {
            $success = 'Deskripsi struktur berhasil diperbarui.';
        } else {
            $error = 'Gagal menyimpan data ke database.';
        }
    }
}

// Get current struktur data
$stmt = $pdo->prepare("SELECT * FROM struktur WHERE id = 1");
$stmt->execute();
$struktur = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Struktur Organisasi - Admin Yayasan</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="edit_page.php?page=tentang">Edit Tentang</a>
            <a href="edit_page.php?page=yayasan">Edit Yayasan</a>
            <a href="edit_struktur.php" class="active">Edit Struktur</a>
            <a href="manage_berita.php">Manage Berita</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>

        <h2>Edit Struktur Organisasi</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_struktur.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Gambar Struktur (JPG/PNG)</label><br />
                <?php if (!empty($struktur['image_path']) && file_exists('../' . $struktur['image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($struktur['image_path']); ?>" alt="Struktur Organisasi" style="max-width: 300px; margin-bottom: 1rem; border-radius: 10px;" />
                <?php else: ?>
                    <p>Tidak ada gambar saat ini.</p>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" />
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($struktur['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>
</html>
