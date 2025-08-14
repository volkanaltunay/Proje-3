<?php
require 'database.php';

// Veritabanı bağlantısını oluştur
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $taskId = $_POST['task_id'];

    try {
        // SQL sorgusunu hazırlama ve çalıştırma
        $sql = "UPDATE tasks SET status = CASE WHEN status = 'completed' THEN 'pending' ELSE 'completed' END WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Task status updated successfully.';
        } else {
            $response['message'] = 'Task not found or status already updated.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>