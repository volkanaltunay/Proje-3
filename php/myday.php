<?php

// database.php dosyasını dahil et
require 'database.php';

// Veritabanı bağlantısını oluştur
$database = new Database();
$db = $database->getConnection();

// --- POST isteği geldiyse (ekleme işlemi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? null;

    if (!empty($title)) {
        try {
            $query = "INSERT INTO tasks (title) VALUES (:title)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Ekleme hatası: " . $e->getMessage();
        }
    }
}

// --- Tüm görevleri (tasks) veritabanından çekme ---
$tasks = [];
try {
    $query = "SELECT id, title, status, created_at FROM tasks WHERE deleted_at IS NULL ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Listeleme hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<style>
    #content-area {
  margin-left: 150px;
  transition: margin-left 0.3s ease;
}
</style>
<body>
<main id="content-area">
    <section center-column>
        <div class="column-top">
            <div class="column-top-left">
                <ul>
                    <li><button><i class="fa-regular fa-sun"></i><span class="title">Günüm</span></button></li>
                    <li><button><i class="fa-solid fa-ellipsis"></i></button></li>
                    <li><button><i class="fa-solid fa-table-cells-large"></i><span>Tablo</span></button></li>
                    <li><button><i class="fa-solid fa-bars-staggered"></i><span>Liste</span></button></li>
                </ul>
            </div>
            <div class="column-top-right">
                <ul>
                    <li><button><i class="fa-solid fa-arrow-down-a-z"></i><span>Sırala</span></button></li>
                    <li><button><i class="fa-solid fa-layer-group"></i><span>Grup</span></button></li>
                    <li><button><i class="fa-regular fa-lightbulb"></i><span>Öneriler</span></button></li>
                </ul>
            </div>
            <div class="column-top-left-date">
                <span class="date"><?php echo date('d F Y'); ?></span>
            </div>
        </div>
        <div class="column-bottom">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="add-Task">
                    <div class="add-TaskNew">
                        <button type="button">
                            <i class="fa-regular fa-circle"></i>
                        </button>
                        <input type="text" name="title" id="task-input" placeholder="Görev Ekle">
                    </div>
                </div>
                <div class="taskCreation">
                    <div class="taskCreation-entrybar-left">
                        <ul>
                            <li><button><i class="fa-solid fa-calendar-days"></i></button></li>
                            <li><button><i class="fa-solid fa-bell"></i></button></li>
                            <li><button><i class="fa-solid fa-repeat"></i></button></li>
                        </ul>
                    </div>
                    <div class="taskCreation-entrybar-right">
                        <button type="submit" aria-label="Ekle">Ekle</button>
                    </div>
                </div>
            </form>
            
            <div class="grid-wiew">
                <div class="grid-wiew-container">
                    <div class="grid-container">
                        <div class="grid-container-header">
                            <ul>
                                <li></li>
                                <li><span class="title">Adı</span></li>
                                <li><span class="date">Tarih</span></li>
                                <li><span class="importance">Önem Derecesi</span></li>
                            </ul>
                        </div>
                        <div class="grid-tasks">
                            <?php if (empty($tasks)): ?>
                                <p style="padding: 10px; text-align: center;">Henüz eklenmiş bir görev yok.</p>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <div class="grid">
                                        <ul>
                                            <li>
                                                <button class="completed">
                                                    <i class="fa-regular fa-circle"></i>
                                                </button>
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
                                                <span class="importance"><i class="fa-solid fa-star"></i></span>
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
</main>
<script>
    window.onload = function() {
        document.getElementById('task-input').value = '';
    };
</script>

</body>
</html>