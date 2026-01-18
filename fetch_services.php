<?php
include('database.php');
if (isset($_POST['type_id'])) {
    $type_id = $_POST['type_id'];

    // ดึงข้อมูลบริการจากฐานข้อมูลที่ตรงกับประเภทบริการ
    $sql = "SELECT * FROM services WHERE type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // ส่งข้อมูลกลับไปยังหน้าเว็บ
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['service_id'] . "'>" . $row['service_name'] . "</option>";
        }
    } else {
        echo "<option value=''>ไม่พบบริการที่ตรงกับประเภทนี้</option>";
    }
}
?>
