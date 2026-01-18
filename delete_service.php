<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
include('database.php'); // เชื่อมต่อกับไฟล์ฐานข้อมูล

// ตรวจสอบว่าได้รับ `service_id` จาก URL หรือไม่
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // คำสั่ง SQL สำหรับการลบบริการ
    $sql_delete = "DELETE FROM services WHERE service_id = '$service_id'";

    if ($conn->query($sql_delete) === TRUE) {
        echo "ลบบริการสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
} else {
    echo "ไม่พบรหัสบริการ";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<br>
<a href="view_services.php">กลับไปยังรายการบริการ</a>
