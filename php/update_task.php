<?php
// update_task.php
require 'database.php';

// Sadece POST isteği ve gerekli veriler varsa işlem yap
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['title'])) {
    $taskId = $_POST['id'];
    $newTitle = $_POST['title'];

    // Verilerin geçerliliğini kontrol et
    if (!empty($newTitle) && filter_var($taskId, FILTER_VALIDATE_INT)) {
        $database = new Database();
        $db = $database->getConnection();

        try {
            // Güvenli bir şekilde UPDATE sorgusu hazırla
            $query = "UPDATE tasks SET title = :title WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $newTitle);
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "Görev başarıyla güncellendi."]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Güncelleme sırasında bir hata oluştu."]);
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