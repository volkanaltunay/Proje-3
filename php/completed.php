<?php
// myday.php
// database.php dosyasını dahil et
require 'database.php';

// Veritabanı bağlantısını oluştur
$database = new Database();
$db = $database->getConnection();

// --- POST isteği geldiyse (ekleme işlemi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    if (!empty($title)) {
        try {
            $query = "INSERT INTO tasks (title) VALUES (:title)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->execute();
        } catch (PDOException $e) {
            http_response_code(500); // Hata durum kodu gönder
            die("Ekleme hatası: " . $e->getMessage());
        }
    }
}

// --- Tüm görevleri (tasks) veritabanından çekme ---
// --- Sadece tamamlanmış görevleri (tasks) veritabanından çekme ---
$tasks = [];
try {
    $query = "SELECT id, title, status, created_at FROM tasks WHERE status = 'completed' AND deleted_at IS NULL ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Listeleme hatası: " . $e->getMessage();
}
?>

<section center-column>
    <div class="column-top">
        <div class="column-top-left">
            <ul>
                <li><button><i class="fa-regular fa-calendar-check"></i><span class="title">Tamamlanan</span></button></li>
                <li><button><i class="fa-solid fa-ellipsis"></i></button></li>
                <li><button><i class="fa-solid fa-table-cells-large"></i><span>Tablo</span></button></li>
                <li><button><i class="fa-solid fa-bars-staggered"></i><span>Liste</span></button></li>
            </ul>
        </div>
        <div class="column-top-right">
            <ul>
                <li><button><i class="fa-solid fa-arrow-down-a-z"></i><span>Sırala</span></button></li>
            </ul>
        </div>
    </div>
    <div class="column-bottom">     
    <div class="grid-wiew">
    <div class="grid-wiew-container">
        <div class="grid-container">
            <div class="grid-container-header">
                <ul>
                    <li><span class="completed"></span></li>
                    <li><span class="title">Adı</span></li>
                    <li><span class="date">Tarih</span></li>
                    <li><span class="importance">Önem Derecesi</span></li>
                </ul>
            </div>
            <div class="grid-tasks">
                <?php if (empty($tasks)): ?>
                    <p style="padding: 10px; text-align: center;">Henüz tamamlanan bir görev yok.</p>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <div class="grid">
                            <ul>
                                <li>
                                    <button type="submit" aria-label="completed"><i class="fa-regular fa-circle"></i></button>
                                </li>
                                <li>
                                    <span class="title"><?php echo htmlspecialchars($task['title']); ?></span>
                                </li>
                                <li>
                                    <span class="date">
                                        <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                                    </span>
                                </li>
                                <li>
                                    <button type="submit" aria-label="importance"><i class="fa-regular fa-star"></i></button>
                                </li>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
