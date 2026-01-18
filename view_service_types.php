<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
    exit();
}

include('database.php');

// ดึงข้อมูลประเภทบริการทั้งหมด
$sql = "SELECT * FROM service_types";
$result = $conn->query($sql);
?>

<h2>รายการประเภทบริการทั้งหมด</h2>
<table border="1">
    <tr>
        <th>ชื่อประเภทบริการ</th>
        <th>คำอธิบาย</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['type_name'] . "</td>
                    <td>" . $row['description'] . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='2'>ไม่มีข้อมูลประเภทบริการ</td></tr>";
    }
    ?>
</table>

<?php
$conn->close();
?>
