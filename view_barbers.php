<?php
session_start();
include('database.php');

// ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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

// ลบช่าง (ถ้ามีการส่งค่ามา)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_barber'])) {
    $barber_id = $_POST['barber_id'];
    $sql_delete = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $barber_id);
    if ($stmt->execute()) {
        $success_message = "✅ ลบข้อมูลช่างเรียบร้อยแล้ว!";
    } else {
        $error_message = "❌ ลบไม่สำเร็จ: " . $conn->error;
    }
    $stmt->close();
}

// ดึงข้อมูลช่างจากฐานข้อมูล
$sql_barbers = "SELECT * FROM users WHERE role = 'barber'";
$result_barbers = $conn->query($sql_barbers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Barbers</title>
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
        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px; /* ระยะห่างระหว่างปุ่ม */
        }
        /* จัดให้เซลล์อยู่ตรงกลาง */
        table th, table td {
            text-align: center;
            vertical-align: middle;
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
  <section class="service_section layout_padding ">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
            VIEWS BARBER
        </h2>
      </div>
  </section>

    <div class="container mt-5">
        <div class="heading_container heading_center">
            <h2 class="text-center">barber list</h2>
        </div>
        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>USENAME</th>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>PHONE NUMBER</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_barbers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td>
                            <a href="edit_barber.php?id=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-sm">EDIT</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="barber_id" value="<?php echo $row['user_id']; ?>">
                                <button type="submit" name="delete_barber" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าจะลบช่างคนนี้?')">DLECTE</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
