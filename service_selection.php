<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าได้ล็อกอินเป็นผู้ใช้หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

// รับ type_id ที่เลือกจาก URL
$type_id = $_GET['type_id'];

// ดึงข้อมูลบริการตามประเภท
$sql = "SELECT s.service_id, s.service_name, s.service_description, s.price 
        FROM services s 
        WHERE s.type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $type_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>เลือกบริการ</h1>

<?php while ($row = $result->fetch_assoc()): ?>
    <div>
        <h3><?php echo $row['service_name']; ?></h3>
        <p><?php echo $row['service_description']; ?></p>
        <p>ราคา: <?php echo number_format($row['price'], 2); ?> บาท</p>
        <!-- ส่ง service_name ไปใน URL -->
        <button onclick="window.location.href='choose_barber.php?service_id=<?php echo $row['service_id']; ?>&service_name=<?php echo urlencode($row['service_name']); ?>'">เลือกบริการ</button>
    </div>
<?php endwhile; ?>
