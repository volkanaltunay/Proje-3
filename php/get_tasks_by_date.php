<?php
// php/get_tasks_by_date.php

header('Content-Type: text/html');
require 'database.php';
$database = new Database();
$db = $database->getConnection();

$tasks = [];
$dueDate = isset($_GET['due_date']) ? $_GET['due_date'] : null;

try {
    if (!empty($dueDate)) {
        $query = "SELECT id, title, status, importance, created_at, due_date FROM tasks 
                  WHERE deleted_at IS NULL AND DATE(due_date) = :due_date 
                  ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "Listeleme hatası: " . $e->getMessage();
}

// HTML çıktısını döndür
if (empty($tasks)): ?>
    <p style="padding: 10px; text-align: center;">Seçilen tarihe ait bir göreviniz yok.</p>
<?php else: ?>
    <?php foreach ($tasks as $task): ?>
        <div class="grid" data-id="<?php echo htmlspecialchars($task['id']); ?>">
            <ul>
                <li>
                    <button type="button" class="completed-btn" aria-label="completed">
                        <i class="<?php echo ($task['status'] === 'completed') ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'; ?>"></i>
                    </button>
                </li>
                <li>
                    <span class="title title-text"><?php echo htmlspecialchars($task['title']); ?></span>
                </li>
                <li>
                    <span class="date">
                        <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                    </span>
                </li>
                <li>
                    <button type="button" class="importance-btn" aria-label="importance">
                        <i class="<?php echo ($task['importance'] === 'important') ? 'fa-solid fa-star' : 'fa-regular fa-star'; ?>"></i>
                    </button>
                </li>
                <li>
                    <button type="button" class="update-btn" aria-label="Güncelle">Güncelle</button>
                </li>
                <li>
                    <button type="button" class="delete-btn" aria-label="Sil">Sil</button>
                </li>
            </ul>
        </div>
    <?php endforeach; ?>
<?php endif; ?>