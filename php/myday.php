<?php

require 'database.php';

$database = new Database();
$db = $database->getConnection();

// --- POST isteği geldiyse (ekleme işlemi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];

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
    $query = "SELECT id, title, status FROM tasks WHERE deleted_at IS NULL ORDER BY created_at DESC";
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
    <title>Document</title>
</head>
<body>
<!-- Sağ İçerik Alanı -->
<main id="content-area">
      <section center-column>
        <div class="column-top">
          <div class="column-top-left">
            <ul>
              <li>
                <button>
                  <i class="fa-regular fa-sun"></i>
                  <span class="title">Günüm</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-ellipsis"></i>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-table-cells-large"></i>
                  <span>Tablo</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-bars-staggered"></i>
                  <span>Liste</span>
                </button>
              </li>
            </ul>
          </div>
          <div class="column-top-right">
            <ul>
              <li>
                <button>
                  <i class="fa-solid fa-arrow-down-a-z"></i>
                  <span>Sırala</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-layer-group"></i>
                  <span>Grup</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-regular fa-lightbulb"></i>
                  <span>Öneriler</span>
                </button>
              </li>
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
                        </div>
                    <div class="taskCreation-entrybar-right">
                        <button type="submit" aria-label="Ekle">Ekle</button>
                    </div>
                </div>
            </form>
            
            <div class="task-list">
                <h3>Bekleyen Görevler</h3>
                <?php if (empty($tasks)): ?>
                    <p>Henüz eklenmiş bir görev yok.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($tasks as $task): ?>
                            <li>
                                <input type="checkbox" <?php echo ($task['status'] === 'completed') ? 'checked' : ''; ?>>
                                <span><?php echo htmlspecialchars($task['title']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
      </section>
    </main>
</body>
</html>