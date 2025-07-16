<?php
require_once '../config.php';
requireLogin();

$pageSlug = isset($_GET['page']) ? $_GET['page'] : '';
$allowedPages = ['tentang', 'yayasan'];

if (!in_array($pageSlug, $allowedPages)) {
    header('Location: dashboard.php');
    exit();
}

$pdo = getConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // allow HTML content

    if (empty($title) || empty($content)) {
        $error = 'Judul dan konten tidak boleh kosong.';
    } else {
        $stmt = $pdo->prepare("UPDATE pages SET title = ?, content = ? WHERE slug = ?");
        if ($stmt->execute([$title, $content, $pageSlug])) {
            $success = 'Halaman berhasil diperbarui.';
        } else {
            $error = 'Terjadi kesalahan saat menyimpan data.';
        }
    }
}

// Get current page data
$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->execute([$pageSlug]);
$pageData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pageData) {
    $error = 'Halaman tidak ditemukan.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Halaman <?php echo htmlspecialchars(ucfirst($pageSlug)); ?> - Admin Yayasan</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="edit_page.php?page=tentang" class="<?php echo $pageSlug == 'tentang' ? 'active' : ''; ?>">Edit Tentang</a>
            <a href="edit_page.php?page=yayasan" class="<?php echo $pageSlug == 'yayasan' ? 'active' : ''; ?>">Edit Yayasan</a>
            <a href="edit_struktur.php">Edit Struktur</a>
            <a href="manage_berita.php">Manage Berita</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>

        <h2>Edit Halaman <?php echo htmlspecialchars(ucfirst($pageSlug)); ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($pageData): ?>
        <form method="POST" action="edit_page.php?page=<?php echo htmlspecialchars($pageSlug); ?>">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($pageData['title']); ?>" required />
            </div>
            <div class="form-group">
                <label for="content">Konten</label>
                <textarea id="content" name="content" class="form-control" required><?php echo htmlspecialchars($pageData['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
