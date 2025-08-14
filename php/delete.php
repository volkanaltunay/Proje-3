<?php
// delete.php
require 'database.php';

// Check if the request method is POST and if the 'id' parameter is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $taskId = $_POST['id'];

    // Validate that the ID is an integer to prevent SQL injection
    if (filter_var($taskId, FILTER_VALIDATE_INT)) {
        $database = new Database();
        $db = $database->getConnection();

        try {
            // Prepare a DELETE statement
            $query = "DELETE FROM tasks WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                http_response_code(200); // OK
                echo json_encode(["success" => true, "message" => "Görev başarıyla silindi."]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(["success" => false, "message" => "Görev silinirken bir hata oluştu."]);
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Veritabanı hatası: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Geçersiz görev ID'si."]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Geçersiz istek."]);
}
?>