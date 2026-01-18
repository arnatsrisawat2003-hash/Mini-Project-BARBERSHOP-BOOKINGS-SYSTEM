<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าลูกค้าได้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$user_id = $_SESSION['user_id']; // user_id จาก session
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'] ?? "Guest";
$email = $row['email'] ?? "N/A";

// ดึงข้อมูลประวัติการจองของลูกค้า
$sql = "
    SELECT 
        b.booking_id,
        s.service_name,
        u.name AS barber_name,
        b.booking_time,
        b.status
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    JOIN users u ON b.barber_id = u.user_id
    WHERE b.customer_id = ?
    ORDER BY b.booking_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการจอง</title>
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

    <style>
    .back-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        color: white;
        background-color: #f7ca00 ; /* ปรับสีเขียว */
        border-radius: 8px; /* ขอบมน */
        transition: all 0.3s ease;
    }
    .text-center {
        text-align: center;
        margin-top: 20px;
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
                bbshop
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
                <li class="nav-item ">
                  <a class="nav-link" href="service_type.php"> Services </a>
                </li>
                <li class="nav-item active">
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
  </div>

  <section class="service_section layout_padding ">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
            history
        </h2>
      </div>
  </section>
<br><br>
<div class="container">
    <div class="heading_container heading_center">
        <h2>
            Booking history
        </h2>
    </div>
    <!-- แสดงข้อความแจ้งเตือน -->
    <div class="booking-section">
        <br>
        <br>
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- ตารางแสดงประวัติการจอง -->
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>SERVICE</th>
                    <th>BARBER</th>
                    <th>DATE TIME</th>
                    <th>STATUS</th>
                    <th>CENCEL</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['barber_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['booking_time']))); ?></td>
                        <td class="status <?php echo htmlspecialchars($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td>
                            <form method="POST" action="cancel_booking.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                <button type="submit" name="delete_barber" class="btn btn-danger btn-sm"  onclick="return confirm('คุณต้องการลบการจองนี้หรือไม่?');">
                                    CENCEL
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">ไม่มีประวัติการจอง</p>
    <?php endif; ?>

    <div class="text-center">
        <a href="customer_dashboard.php" class="back-button">กลับสู่หน้าหลัก</a>
    </div>
</div>

</body>
</html>
