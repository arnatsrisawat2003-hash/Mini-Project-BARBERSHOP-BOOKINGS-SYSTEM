<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['service_id'])) {
    $service_id = $_POST['service_id'];

    $stmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete']);
    }
}
?>
