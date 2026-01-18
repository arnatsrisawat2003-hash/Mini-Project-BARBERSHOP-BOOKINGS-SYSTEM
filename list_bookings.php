<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'barber') {
    header("Location: login.php");
    exit();
}

$barber_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = $_SESSION['user_id']; // user_id จาก session
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'] ?? "Guest";
$email = $row['email'] ?? "N/A";

// ตรวจสอบการอัปเดตสถานะการจอง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_id'])) {
    $booking_id = intval($_POST['confirm_booking_id']);
    $sql_update_status = "UPDATE bookings SET status = 'confirmed' WHERE booking_id = ? AND barber_id = ?";
    $stmt = $conn->prepare($sql_update_status);
    $stmt->bind_param("ii", $booking_id, $barber_id);
    $stmt->execute();
}

// ดึงข้อมูลช่างที่ล็อกอิน
$sql_barber_info = "SELECT name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_barber_info);
$stmt->bind_param("i", $barber_id);
$stmt->execute();
$result_barber = $stmt->get_result();
$barber = $result_barber->fetch_assoc();

// ดึงข้อมูลการจองที่มีสถานะเป็น 'pending' สำหรับช่างที่ล็อกอิน
$sql_bookings = "
    SELECT 
        b.booking_id,
        c.user_id AS customer_id,
        c.name AS customer_name,
        s.service_name,
        b.booking_time,
        b.status
    FROM bookings b
    JOIN users c ON b.customer_id = c.user_id
    JOIN services s ON b.service_id = s.service_id
    WHERE b.barber_id = ? AND b.status = 'pending'
    ORDER BY b.booking_time ASC
";

$stmt = $conn->prepare($sql_bookings);
$stmt->bind_param("i", $barber_id);
$stmt->execute();
$result_bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reservations</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"
        rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">   

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
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
              <span>bbshop</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>
            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="barber_dashboard.php">Home</a>
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
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
            Pending Reservations
        </h2>
      </div>
  </section>
  <div class="container">
    <div class="booking-section">
        <br>
        <br>
        <?php if ($result_bookings->num_rows > 0): ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Date Time</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; ?>
                    <?php while ($row = $result_bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['booking_time']))); ?></td>
                            <td class="status <?php echo htmlspecialchars($row['status']); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td>
                                <!-- เช็คข้อมูลลูกค้า -->
                                <form method="GET" action="customer_info.php" style="display:inline;">
                                    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($row['customer_id']); ?>">
                                    <button type="submit" class="action-btn">Check customer </button>
                                </form>

                                <!-- ยืนยันการจอง -->
                                <form method="POST" action="confirm_booking.php" style="display:inline;">
                                    <input type="hidden" name="confirm_booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                    <button type="submit" class="btn btn-success">Confirm</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">ไม่มีรายการการจองที่ยังไม่ได้ยืนยัน</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
