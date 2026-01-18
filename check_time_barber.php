<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

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



// ดึงข้อมูลช่างทั้งหมดจากตาราง users ที่มี role เป็น 'barber'
$sql_barbers = "SELECT user_id, name FROM users WHERE role = 'barber'";
$result_barbers = $conn->query($sql_barbers);

// ตรวจสอบว่าเลือกช่างแล้วหรือยัง
$barber_id = isset($_GET['barber_id']) ? intval($_GET['barber_id']) : 0;

// ถ้ามีการเลือกช่าง
if ($barber_id > 0) {
    // ดึงข้อมูลตารางเวลาของช่างที่เลือก (ลบส่วนที่เกี่ยวกับ service_id ออก)
    $sql_barber_time = "
    SELECT bt.day_of_week, bt.start_time, bt.end_time, bt.time_slot 
    FROM barber_time bt
    WHERE bt.barber_id = ? 
    ORDER BY FIELD(bt.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ";
    $stmt = $conn->prepare($sql_barber_time);
    $stmt->bind_param("i", $barber_id);
    $stmt->execute();
    $result_barber_time = $stmt->get_result();

    // ตรวจสอบว่า query ติดตามช่าง
    if ($result_barber_time->num_rows > 0) {
        // ดึงข้อมูลชื่อช่าง
        $sql_barber_name = "SELECT name FROM users WHERE user_id = ?";
        $stmt_name = $conn->prepare($sql_barber_name);
        $stmt_name->bind_param("i", $barber_id);
        $stmt_name->execute();
        $result_name = $stmt_name->get_result();
        
        if ($result_name->num_rows > 0) {
            $barber_row = $result_name->fetch_assoc();
            $barber_name = $barber_row['name'];
        } else {
            $barber_name = "ไม่พบข้อมูลช่าง";
        }
    } else {
        $barber_name = "ไม่พบข้อมูลตารางเวลาของช่างนี้";
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางเวลาของช่าง</title>
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
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .container {
            font-family: "Bebas Neue", sans-serif;
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .select-barber {
            margin-bottom: 20px;
        }
        .select-barber select {
            padding: 10px;
            width: 100%;
            font-size: 16px;
        }
    </style>
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
            <a class="navbar-brand" href="admin_dashboard.php">
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
                    <a class="nav-link" href="admin_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($email); ?></a>
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

<div class="container">
    <h1>Technician's schedule</h1>

    <!-- ฟอร์มเลือกช่าง -->
    <div class="select-barber">
        <form action="" method="GET">
            <label for="barber_id">Select Barber:</label>
            <select name="barber_id" id="barber_id" onchange="this.form.submit()">
                <option value="">-- Select Barber --</option>
                <?php while ($row = $result_barbers->fetch_assoc()): ?>
                    <option value="<?php echo $row['user_id']; ?>" <?php echo $barber_id == $row['user_id'] ? 'selected' : ''; ?>>
                        <?php echo $row['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <!-- แสดงตารางเวลาหากเลือกช่าง -->
    <?php if ($barber_id > 0 && isset($barber_name)): ?>
        <h2>Timetable of <?php echo htmlspecialchars($barber_name); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>ช่วงเวลา</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_time = $result_barber_time->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row_time['day_of_week']; ?></td>
                        <td><?php echo $row_time['time_slot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Please select a technician to show the schedule.</p>
    <?php endif; ?>
</div>

</body>
</html>
