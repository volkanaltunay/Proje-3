<?php
header('Content-Type: application/json');

// Database connection details
$servername = "your_servername";
$username = "your_username";
$password = "your_password";
$dbname = "your_dbname";

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $taskId = $_POST['task_id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query to update the task status
        $sql = "UPDATE tasks SET status = 'completed' WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Task status updated to completed.';
        } else {
            $response['message'] = 'Task not found or status already completed.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } finally {
        $conn = null;
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>