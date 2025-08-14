<?php
require 'database.php';
$database = new Database();
$db = $database->getConnection();

// Sadece önemli görevleri al
$tasks = [];
try {
    $query = "SELECT id, title, status, importance, created_at 
              FROM tasks 
              WHERE importance = 'important' AND deleted_at IS NULL 
              ORDER BY created_at DESC";
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
                <li>
                    <button><i class="fa-solid fa-star" style="color:#2564cf"></i>
                        <span class="title">Önemli</span>
                    </button>
                </li>
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
                            <p style="padding: 10px; text-align: center;">
                                Henüz önemli olarak işaretlenmiş bir görev yok.
                            </p>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <div class="grid <?php echo ($task['status'] == 'completed') ? 'completed-task' : ''; ?>" 
                                     data-id="<?php echo htmlspecialchars($task['id']); ?>">
                                    <ul>
                                        <li>
                                            <button type="button" class="completed-btn" aria-label="completed">
                                                <i class="<?php echo ($task['status'] == 'completed') 
                                                    ? 'fa-solid fa-circle-check' 
                                                    : 'fa-regular fa-circle'; ?>"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <span class="title title-text">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="date">
                                                <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                                            </span>
                                        </li>
                                        <li>
                                            <button type="button" class="importance-btn" aria-label="importance">
                                                <i class="fa-solid fa-star" style="color:#2564cf"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="update-btn">Güncelle</button>
                                        </li>
                                        <li>
                                            <button type="button" class="delete-btn">Sil</button>
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


