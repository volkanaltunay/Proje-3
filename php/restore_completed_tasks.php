<?php
// php/restore_completed_tasks.php

require 'database.php';
$database = new Database();
$db = $database->getConnection();

$response = [
    'success' => false,
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Tamamlanmış tüm görevlerin durumunu 'pending' olarak güncelle
        $updateQuery = "UPDATE tasks SET status = 'pending' WHERE status = 'completed'";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute();
        
        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            $response['success'] = true;
            $response['message'] = $rowCount . ' görev listeye geri getirildi.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Geri getirilecek tamamlanmış görev bulunamadı.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Veritabanı hatası: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Geçersiz istek.';
}

echo json_encode($response);
?>
