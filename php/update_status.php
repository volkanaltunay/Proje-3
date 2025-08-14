<?php
// update_status.php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['status'])) {
    $taskId = $_POST['id'];
    $newStatus = $_POST['status'];

    if (filter_var($taskId, FILTER_VALIDATE_INT) && ($newStatus == 0 || $newStatus == 1)) {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "UPDATE tasks SET status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $newStatus, PDO::PARAM_INT);
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "Görev durumu güncellendi."]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Durum güncellenirken bir hata oluştu."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Veritabanı hatası: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Geçersiz veri girişi."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Geçersiz istek."]);
}
?>