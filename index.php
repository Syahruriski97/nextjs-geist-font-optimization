<?php
require_once 'config.php';

// Get current page from URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Get database connection
$pdo = getConnection();

// Get page content from database
function getPageContent($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get latest news
function getLatestNews($pdo, $limit = 3) {
    $stmt = $pdo->prepare("SELECT * FROM berita ORDER BY date DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get struktur data
function getStruktur($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM struktur LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all news for berita page
function getAllNews($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM berita ORDER BY date DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yayasan - <?php echo ucfirst($page); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav-container">
                <a href="index.php" class="logo">Yayasan</a>
                <ul class="nav-menu">
                    <li><a href="index.php?page=home" class="<?php echo $page == 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="index.php?page=tentang" class="<?php echo $page == 'tentang' ? 'active' : ''; ?>">Tentang</a></li>
                    <li><a href="index.php?page=yayasan" class="<?php echo $page == 'yayasan' ? 'active' : ''; ?>">Yayasan</a></li>
                    <li><a href="index.php?page=struktur" class="<?php echo $page == 'struktur' ? 'active' : ''; ?>">Struktur</a></li>
                    <li><a href="index.php?page=berita" class="<?php echo $page == 'berita' ? 'active' : ''; ?>">Berita</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <?php if ($page == 'home'): ?>
            <!-- Hero Section -->
            <section class="hero">
                <div class="container">
                    <div class="hero-content">
                        <h1>Menyekolahkan Santri, Bukan Menyantrikkan Anak Sekolah</h1>
                        <p>Membangun generasi yang berakhlak mulia dan berprestasi untuk masa depan yang lebih baik</p>
                        <a href="index.php?page=tentang" class="btn">Profil</a>
                        <a href="index.php?page=yayasan" class="btn btn-secondary">Pendaftaran</a>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="main-content">
                <div class="container">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">👥</div>
                            <div class="stat-number">2.879</div>
                            <div class="stat-label">Total Jumlah Santri</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">👨</div>
                            <div class="stat-number">1.169</div>
                            <div class="stat-label">Santri Wustha Putra</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">👩</div>
                            <div class="stat-number">603</div>
                            <div class="stat-label">Santri Ulya Putra</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🎓</div>
                            <div class="stat-number">226</div>
                            <div class="stat-label">Kuliyyatul Mu'allimin</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">👨‍🎓</div>
                            <div class="stat-number">559</div>
                            <div class="stat-label">Santri Wustha Putri</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">👩‍🎓</div>
                            <div class="stat-number">322</div>
                            <div class="stat-label">Santri Ulya Putri</div>
                        </div>
                    </div>

                    <!-- Latest News -->
                    <div class="content-section">
                        <h2>Berita Terbaru</h2>
                        <div class="news-grid">
                            <?php
                            $latestNews = getLatestNews($pdo);
                            foreach ($latestNews as $news):
                            ?>
                            <div class="news-card">
                                <div class="news-card-content">
                                    <div class="news-date"><?php echo formatDate($news['date']); ?></div>
                                    <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                                    <p class="news-excerpt"><?php echo substr(htmlspecialchars($news['content']), 0, 150) . '...'; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="text-align: center; margin-top: 2rem;">
                            <a href="index.php?page=berita" class="btn">Lihat Semua Berita</a>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($page == 'tentang'): ?>
            <section class="main-content">
                <div class="container">
                    <?php
                    $pageContent = getPageContent($pdo, 'tentang');
                    if ($pageContent):
                    ?>
                    <div class="content-section">
                        <h2><?php echo htmlspecialchars($pageContent['title']); ?></h2>
                        <div><?php echo nl2br(htmlspecialchars($pageContent['content'])); ?></div>
                    </div>
                    <?php else: ?>
                    <div class="content-section">
                        <h2>Tentang Kami</h2>
                        <p>Konten belum tersedia.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

        <?php elseif ($page == 'yayasan'): ?>
            <section class="main-content">
                <div class="container">
                    <?php
                    $pageContent = getPageContent($pdo, 'yayasan');
                    if ($pageContent):
                    ?>
                    <div class="content-section">
                        <h2><?php echo htmlspecialchars($pageContent['title']); ?></h2>
                        <div><?php echo nl2br(htmlspecialchars($pageContent['content'])); ?></div>
                    </div>
                    <?php else: ?>
                    <div class="content-section">
                        <h2>Yayasan</h2>
                        <p>Konten belum tersedia.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

        <?php elseif ($page == 'struktur'): ?>
            <section class="main-content">
                <div class="container">
                    <div class="content-section">
                        <h2>Struktur Organisasi</h2>
                        <?php
                        $struktur = getStruktur($pdo);
                        if ($struktur):
                        ?>
                        <?php if (!empty($struktur['image_path']) && file_exists($struktur['image_path'])): ?>
                        <div style="text-align: center; margin: 2rem 0;">
                            <img src="<?php echo htmlspecialchars($struktur['image_path']); ?>" alt="Struktur Organisasi" style="max-width: 100%; height: auto; border-radius: 10px;">
                        </div>
                        <?php endif; ?>
                        <div><?php echo nl2br(htmlspecialchars($struktur['description'])); ?></div>
                        <?php else: ?>
                        <p>Struktur organisasi belum tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($page == 'berita'): ?>
            <section class="main-content">
                <div class="container">
                    <div class="content-section">
                        <h2>Semua Berita</h2>
                        <div class="news-grid">
                            <?php
                            $allNews = getAllNews($pdo);
                            foreach ($allNews as $news):
                            ?>
                            <div class="news-card">
                                <div class="news-card-content">
                                    <div class="news-date"><?php echo formatDate($news['date']); ?></div>
                                    <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                                    <p class="news-excerpt"><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (empty($allNews)): ?>
                        <p>Belum ada berita yang tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Yayasan. Semua hak dilindungi.</p>
        </div>
    </footer>
</body>
</html>
