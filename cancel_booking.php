<?php
session_start();
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];

    $sql = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "ยกเลิกการจองสำเร็จ!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบการจอง";
    }

    $stmt->close();
    $conn->close();

    header("Location: history.php");
    exit();
}
?>
