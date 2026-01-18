<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('database.php');

// ดึงข้อมูลช่วงเวลาของช่างทั้งหมด
$sql_barber_time = "SELECT bt.time_id, u.name, bt.day_of_week, bt.time_slot
                    FROM barber_time bt
                    JOIN users u ON bt.barber_id = u.user_id
                    WHERE u.role = 'barber'";

$result_barber_time = $conn->query($sql_barber_time);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ช่วงเวลาและวันทำงานของช่าง</title>
</head>
<body>
    <h2>ช่วงเวลาและวันทำงานของช่าง</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ชื่อช่าง</th>
                <th>วันในสัปดาห์</th>
                <th>ช่วงเวลา</th>
                <th>การกระทำ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_barber_time->num_rows > 0) {
                while ($row = $result_barber_time->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['day_of_week'] . "</td>";
                    echo "<td>" . $row['time_slot'] . "</td>";
                    echo "<td><a href='delete_barber_time.php?id=" . $row['time_id'] . "' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าจะลบช่วงเวลานี้?\")'>ลบ</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>ไม่มีข้อมูลช่วงเวลาและวันทำงานของช่าง</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br>
    <a href="admin_dashboard.php">กลับไปยังหน้าแดชบอร์ด</a>

    <?php
    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
    ?>
</body>
</html>
