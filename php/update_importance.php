<?php
require 'database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $taskId = intval($_POST['task_id']);

    // Mevcut importance değerini öğren
    $stmt = $db->prepare("SELECT importance FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $taskId);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        $newImportance = ($task['importance'] === 'important') ? 'normal' : 'important';

        $update = $db->prepare("UPDATE tasks SET importance = :importance WHERE id = :id");
        $update->bindParam(':importance', $newImportance);
        $update->bindParam(':id', $taskId);
        $update->execute();

        echo json_encode(['success' => true, 'importance' => $newImportance]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Görev bulunamadı.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
}
?>
