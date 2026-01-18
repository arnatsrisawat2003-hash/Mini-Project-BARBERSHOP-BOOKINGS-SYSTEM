<?php
session_start();
include('database.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // user_id จาก session
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    $name = "Guest";
    $email = "N/A";
}

// ตรวจสอบว่ามีการเลือกช่างหรือไม่
$barber_id = isset($_GET['barber_id']) ? intval($_GET['barber_id']) : 0;

// ถ้ามีการเลือกช่าง
if ($barber_id > 0) {
    // ดึงข้อมูลตารางเวลาของช่างที่เลือก
    $sql_barber_time = "
    SELECT time_id, day_of_week, start_time, end_time, time_slot 
    FROM barber_time 
    WHERE barber_id = ? 
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ";
    $stmt = $conn->prepare($sql_barber_time);
    $stmt->bind_param("i", $barber_id);
    $stmt->execute();
    $result_barber_time = $stmt->get_result();

    // ลบเวลาทำงาน
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_time'])) {
        $time_id = $_POST['time_id'];
        
        // ลบข้อมูลจากตาราง barber_time
        $sql_delete_time = "DELETE FROM barber_time WHERE time_id = ?";
        $stmt = $conn->prepare($sql_delete_time);
        $stmt->bind_param("i", $time_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขเวลาทำงานของช่าง</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <script type="text/javascript">
        // ฟังก์ชันสำหรับการแสดงกล่องแจ้งเตือนก่อนลบ
        function confirmDelete(time_id) {
            if (confirm("คุณต้องการลบเวลานี้หรือไม่?")) {
                // ถ้าผู้ใช้กดยืนยันให้ลบข้อมูล
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "time_id";
                input.value = time_id;
                form.appendChild(input);
                var inputDelete = document.createElement("input");
                inputDelete.type = "hidden";
                inputDelete.name = "delete_time";
                form.appendChild(inputDelete);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>
<body class="sub_page">
  <div class="hero_area">
    <!-- header section starts -->
    <div class="hero_bg_box">
      <div class="img-box">
        <img src="img/haircut-4019676_1920.jpg" alt="">
      </div>
    </div>
    <header class="header_section">
      <div class="header_bottom">
        <div class="container-fluid">
          <nav class="navbar navbar-expand-lg custom_nav-container">
            <a class="navbar-brand" href="barber_dashboard.php">
              <span>BBshop</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>
            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav">
                <li class="nav-item active">
                  <a class="nav-link" href="admin_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($email); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Log out</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </header>
  </div>
    
  <section class="service_section layout_padding ">
      <div class="heading_container heading_center">
        <h2>
            EDIT INFO BARBER
        </h2>
  </section>
  <br>
<div class="container">
    <!-- ตารางเวลาของช่าง -->
    <?php if ($barber_id > 0): ?>
        <div class="heading_container heading_center">
        <h2>
            EDIT INFO BARBER
        </h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_time = $result_barber_time->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row_time['day_of_week']; ?></td>
                        <td><?php echo $row_time['time_slot']; ?></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row_time['time_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
