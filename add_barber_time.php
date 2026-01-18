<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('database.php');

$user_id = $_SESSION['user_id'];
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barber_id = $_POST['barber_id'];
    $day_of_week = $_POST['day_of_week'];
    $time_slots = $_POST['time_slots'];

    $sql_delete = "DELETE FROM barber_time WHERE barber_id = ? AND day_of_week = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("is", $barber_id, $day_of_week);
    $stmt->execute();

    foreach ($time_slots as $time_slot) {
        $sql_insert = "INSERT INTO barber_time (barber_id, day_of_week, time_slot) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("iss", $barber_id, $day_of_week, $time_slot);
        $stmt->execute();
    }

    $_SESSION['success'] = "เพิ่มเวลาของช่างเรียบร้อย!";
    header("Location: ".$_SERVER['PHP_SELF']); // รีเฟรชหน้าเพื่อแสดงข้อความ
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มช่วงเวลาให้กับช่าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
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
        .card-header {
    background-color: black !important;
    }
    .card-header h3 {
    color: white !important;
    }
    .btn-success {
    background-color: gold !important;
    border-color: gold !important;
    color: black !important;
}

.btn-success:hover {
    background-color: darkgoldenrod !important;
    border-color: darkgoldenrod !important;
}

    </style>
</head>
<body class="sub_page">
    <?php
// ตรวจสอบว่ามีข้อความแจ้งเตือนหรือไม่
        if (isset($_SESSION['success'])) {
        echo "<script>alert('" . $_SESSION['success'] . "');</script>";
        unset($_SESSION['success']); // ล้างค่าเพื่อไม่ให้แจ้งเตือนซ้ำ
        }
    ?>
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
            <a class="navbar-brand" href="index.html">
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
                  <a class="nav-link" href="admin_dashboard.php">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($email); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="logout.php">Loggut</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </header>
    <!-- end header section -->
  </div>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3>เพิ่มช่วงเวลาให้กับช่าง</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="barber_id" class="form-label">เลือกช่าง:</label>
                        <select name="barber_id" class="form-select" required>
                            <option value="">-- เลือกช่าง --</option>
                            <?php
                            include('database.php');
                            $sql_barbers = "SELECT user_id, name FROM users WHERE role = 'barber'";
                            $result_barbers = $conn->query($sql_barbers);
                            if ($result_barbers->num_rows > 0) {
                                while ($row = $result_barbers->fetch_assoc()) {
                                    echo "<option value='" . $row['user_id'] . "'>" . $row['name'] . "</option>";
                                }
                            }
                            $conn->close();
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="day_of_week" class="form-label">วันในสัปดาห์:</label>
                        <select name="day_of_week" class="form-select" required>
                            <option value="">-- เลือกวัน --</option>
                            <option value="Monday">จันทร์</option>
                            <option value="Tuesday">อังคาร</option>
                            <option value="Wednesday">พุธ</option>
                            <option value="Thursday">พฤหัสบดี</option>
                            <option value="Friday">ศุกร์</option>
                            <option value="Saturday">เสาร์</option>
                            <option value="Sunday">อาทิตย์</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เลือกช่วงเวลา:</label>
                        <div class="row">
                            <?php
                            $time_slots = [
                                "08:00-09:00", "09:00-10:00", "10:00-11:00", "11:00-12:00",
                                "12:00-13:00", "13:00-14:00", "14:00-15:00", "15:00-16:00", "16:00-17:00"
                            ];
                            foreach ($time_slots as $slot) {
                                echo "<div class='col-md-4'>
                                        <div class='form-check'>
                                            <input class='form-check-input' type='checkbox' name='time_slots[]' value='$slot'>
                                            <label class='form-check-label'>$slot</label>
                                        </div>
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">เพิ่มเวลา</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปยังหน้าแดชบอร์ด</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
