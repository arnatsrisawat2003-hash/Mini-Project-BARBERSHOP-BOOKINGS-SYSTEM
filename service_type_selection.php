<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าได้ล็อกอินเป็นผู้ใช้หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลประเภทบริการจากฐานข้อมูล
$sql = "SELECT * FROM service_types";
$result = $conn->query($sql);
?>

<h1>เลือกประเภทบริการ</h1>

<?php while ($row = $result->fetch_assoc()): ?>
    <div>
        <h3><?php echo $row['type_name']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <button onclick="window.location.href='service_selection.php?type_id=<?php echo $row['type_id']; ?>'">เลือกบริการ</button>
    </div>
<?php endwhile; ?>

