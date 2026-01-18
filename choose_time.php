<?php 
session_start();
include('database.php');

// ฟังก์ชันสำหรับสร้าง UUID
function generate_uuid() {
    return bin2hex(random_bytes(16));
}

// ตรวจสอบว่าได้ล็อกอินเป็นผู้ใช้หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
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


// รับข้อมูลจาก URL
$service_id = $_GET['service_id'];
$barber_id = $_GET['barber_id'];
$booking_date = $_GET['booking_date'];

// สร้าง UUID สำหรับ booking_id
$booking_id = generate_uuid();

// ดึงข้อมูลบริการ
$sql_service = "SELECT service_name FROM services WHERE service_id = ?";
$stmt_service = $conn->prepare($sql_service);
$stmt_service->bind_param("i", $service_id);
$stmt_service->execute();
$result_service = $stmt_service->get_result();
$service = $result_service->fetch_assoc();

// ดึงข้อมูลเวลาที่ช่างว่างในวันที่ลูกค้าเลือก
$sql_time = "SELECT * FROM barber_time WHERE barber_id = ? AND day_of_week = ?";
$stmt_time = $conn->prepare($sql_time);
$day_of_week = date('l', strtotime($booking_date));  // วันของสัปดาห์จากวันที่จอง
$stmt_time->bind_param("is", $barber_id, $day_of_week);
$stmt_time->execute();
$result_time = $stmt_time->get_result();

// ถ้ามีการเลือกเวลาในฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_time'])) {
    $selected_time = $_POST['booking_time'];
    $booking_start = $booking_date . ' ' . $selected_time;

    // ใช้ DateTime เพื่อจัดการเวลา
    $timezone = new DateTimeZone('Asia/Bangkok');
    $booking_start_dt = new DateTime($booking_start, $timezone);
    $booking_start_dt = $booking_start_dt->format('Y-m-d H:i:s');

    // ตรวจสอบว่าเวลานี้มีการจองในฐานข้อมูลแล้วหรือไม่ (เช็คตาม barber_id, service_id, และ booking_date)
    $sql_check = "SELECT * FROM bookings WHERE barber_id = ? AND service_id = ? AND DATE(booking_time) = ? AND booking_time = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iiss", $barber_id, $service_id, $booking_date, $booking_start_dt);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $message = "เวลานี้ถูกจองแล้ว กรุณาเลือกเวลาอื่น";
    } else {
        $customer_id = $_SESSION['user_id'];
        // บันทึกการจองใน bookings
        $sql_booking = "INSERT INTO bookings (booking_id, customer_id, barber_id, service_id, booking_time, status) 
                        VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt_booking = $conn->prepare($sql_booking);
        $stmt_booking->bind_param("siiss", $booking_id, $customer_id, $barber_id, $service_id, $booking_start_dt);
        $stmt_booking->execute();

        $message = "การจองของคุณสำเร็จ กรุณารอตรวจสอบสถานะการจอง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกช่างตัดผม - <?php echo htmlspecialchars($service['service_name']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap');
table {
    width: 100%;
    border-collapse: collapse;
    font-family: "Bebas Neue", sans-serif;
    font-weight: 400;
    font-style: normal;
    letter-spacing: 1px;
}


thead {
    background-color: black;
    color: white;
}

th, td {
    padding: 12px;
    text-align: left;
    font-weight: normal; /* ทำให้ข้อความไม่หนา */
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* ลบเส้นขอบของตารางทุกเส้น */
table, th, td {
    border: none;
}

/* ปรับแต่งปุ่ม */
.action-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: normal; /* ปรับให้ข้อความไม่หนา */
    transition: background 0.3s ease;
}

/* ปุ่มเช็คข้อมูลลูกค้า */
.action-btn:first-of-type {
    background-color: #007bff; /* สีน้ำเงิน */
    color: white;
}

.action-btn:first-of-type:hover {
    background-color: #0056b3; /* น้ำเงินเข้มเมื่อ hover */
}

/* ปุ่มยืนยัน */
.action-btn:nth-of-type(2) {
    background-color: #28a745; /* สีเขียว */
    color: white;
}

.action-btn:nth-of-type(2):hover {
    background-color: #1e7e34; /* เขียวเข้มเมื่อ hover */
}

/* ปุ่มปฏิเสธ */
.action-btn.reject {
    background-color: #dc3545; /* สีแดง */
    color: white;
}

.action-btn.reject:hover {
    background-color: #a71d2a; /* แดงเข้มเมื่อ hover */
}

confirm-btn{
    background-color: #28a745; /* สีเขียว */
}
.kuy:link, a:visited {
            background-color: #f1db25;
            color:  white;
            border: 2px solid #f1db25;
            padding: 5px 30px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .kuy:hover, a:active {
            background-color: white;
            color: #f1db25;
        }

    </style>
</head>
<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
    <div class="hero_bg_box">
      <div class="img-box">
        <img src="img/haircut-4019676_1920.jpg" alt="">
      </div>
    </div>

    <header class="header_section">
      <div class="header_bottom">
        <div class="container-fluid">
          <nav class="navbar navbar-expand-lg custom_nav-container">
            <a class="navbar-brand" href="customer_dashboard.php">
              <span>
                BBshop
              </span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav  ">
                <li class="nav-item ">
                  <a class="nav-link" href="customer_dashboard.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                  <a class="nav-link" href="service_type.php"> Services </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="history.php">History</a>
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
    <!-- end header section -->
  </div>

  <section class="service_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Service <span>Type</span></h2>
        </div>
</section>
<br>
<br>
<div class="container">

<div align="center">
    <h1>เลือกเวลาการจองสำหรับบริการ: <?php echo htmlspecialchars($service['service_name']); ?></h1>
    <h2>วันที่เลือก: <?php echo htmlspecialchars($booking_date); ?></h2>
</div>

    <?php if (isset($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <table>
          <thead>
            <tr>
                <th>Time Select</th>
                <th>Status</th>
                <th>Select</th>
            </tr>
          </thead>
            <?php while ($row = $result_time->fetch_assoc()): ?>
                <?php 
                    $time_slot = $row['time_slot'];
                    $booking_start = $booking_date . ' ' . $time_slot;
                    $timezone = new DateTimeZone('Asia/Bangkok');
                    $booking_start_dt = new DateTime($booking_start, $timezone);
                    $booking_start_dt = $booking_start_dt->format('Y-m-d H:i:s');

                    // ตรวจสอบสถานะการจองจาก bookings
                    $sql_check_booking = "SELECT * FROM bookings WHERE barber_id = ? AND service_id = ? AND DATE(booking_time) = ? AND booking_time = ?";
                    $stmt_check_booking = $conn->prepare($sql_check_booking);
                    $stmt_check_booking->bind_param("iiss", $barber_id, $service_id, $booking_date, $booking_start_dt);
                    $stmt_check_booking->execute();
                    $result_check_booking = $stmt_check_booking->get_result();
                    $is_unavailable = $result_check_booking->num_rows > 0;
                ?>
                <tr>
                    <td><?php echo $time_slot; ?></td>
                    <td><?php echo $is_unavailable ? 'unavailable' : 'available'; ?></td>
                    <td>
                    <button type="submit" name="booking_time" value="<?php echo $time_slot; ?>"
                        class="btn btn-success <?php echo $is_unavailable ? 'unavailable' : 'available'; ?>"
                        <?php echo $is_unavailable ? 'disabled' : ''; ?>
                        onclick="return confirmBooking('<?php echo $time_slot; ?>');">
                        <?php echo $is_unavailable ? 'Can t Select' : 'Select Time'; ?>
                    </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <input type="hidden" name="barber_id" value="<?php echo $barber_id; ?>">
        <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
    </form>
    <br>
    <a href="customer_dashboard.php" class="kuy">Back</a>
</div>
</div>

<script>
function confirmBooking(time) {
    return confirm("คุณแน่ใจหรือไม่ว่าต้องการจองเวลา " + time + " ?");
}
</script>

</body>
</html>
