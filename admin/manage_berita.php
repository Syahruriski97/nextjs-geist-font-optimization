<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();
$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Berita berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus berita.';
    }
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $date = $_POST['date'];

    if (empty($title) || empty($content) || empty($date)) {
        $error = 'Semua field harus diisi.';
    } else {
        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE berita SET title = ?, content = ?, date = ? WHERE id = ?");
            if ($stmt->execute([$title, $content, $date, $id])) {
                $success = 'Berita berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui berita.';
            }
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO berita (title, content, date) VALUES (?, ?, ?)");
            if ($stmt->execute([$title, $content, $date])) {
                $success = 'Berita berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan berita.';
            }
        }
    }
}

// Get berita list
$stmt = $pdo->prepare("SELECT * FROM berita ORDER BY date DESC");
$stmt->execute();
$beritaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get berita to edit if edit param exists
$editBerita = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$editId]);
    $editBerita = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Berita - Admin Yayasan</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="edit_page.php?page=tentang">Edit Tentang</a>
            <a href="edit_page.php?page=yayasan">Edit Yayasan</a>
            <a href="edit_struktur.php">Edit Struktur</a>
            <a href="manage_berita.php" class="active">Manage Berita</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>

        <h2>Manage Berita</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <h3><?php echo $editBerita ? 'Edit Berita' : 'Tambah Berita'; ?></h3>
        <form method="POST" action="manage_berita.php">
            <input type="hidden" name="id" value="<?php echo $editBerita ? $editBerita['id'] : 0; ?>" />
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo $editBerita ? htmlspecialchars($editBerita['title']) : ''; ?>" required />
            </div>
            <div class="form-group">
                <label for="content">Konten</label>
                <textarea id="content" name="content" class="form-control" required><?php echo $editBerita ? htmlspecialchars($editBerita['content']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="date" id="date" name="date" class="form-control" value="<?php echo $editBerita ? $editBerita['date'] : date('Y-m-d'); ?>" required />
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $editBerita ? 'Update' : 'Tambah'; ?></button>
            <?php if ($editBerita): ?>
                <a href="manage_berita.php" class="btn btn-secondary" style="margin-left: 1rem;">Batal</a>
            <?php endif; ?>
        </form>

        <h3>Daftar Berita</h3>
        <?php if (count($beritaList) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($beritaList as $berita): ?>
                <tr>
                    <td><?php echo htmlspecialchars($berita['title']); ?></td>
                    <td><?php echo formatDate($berita['date']); ?></td>
                    <td>
                        <a href="manage_berita.php?edit=<?php echo $berita['id']; ?>">Edit</a> |
                        <a href="manage_berita.php?delete=<?php echo $berita['id']; ?>" onclick="return confirm('Yakin ingin menghapus berita ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Belum ada berita yang tersedia.</p>
        <?php endif; ?>
    </div>
</body>
</html>
