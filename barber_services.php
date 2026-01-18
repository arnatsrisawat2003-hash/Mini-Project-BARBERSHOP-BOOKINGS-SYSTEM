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
$row = $result->fetch_assoc();
$name = $row['name'] ?? "Guest";
$email = $row['email'] ?? "N/A";

// ดึงข้อมูลบริการทั้งหมด
$sql_services = "SELECT * FROM services";
$stmt = $conn->prepare($sql_services);
$stmt->execute();
$result_services = $stmt->get_result();

// การลบบริการ
if (isset($_GET['delete_service'])) {
    $service_id = $_GET['delete_service'];

    // ลบบริการจากตาราง `services` และ `barber_services`
    $delete_sql = "DELETE FROM services WHERE service_id = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("i", $service_id);
    $stmt_delete->execute();

    // ลบข้อมูลที่เกี่ยวข้องใน `barber_services`
    $delete_barber_service_sql = "DELETE FROM barber_services WHERE service_id = ?";
    $stmt_delete_barber = $conn->prepare($delete_barber_service_sql);
    $stmt_delete_barber->bind_param("i", $service_id);
    $stmt_delete_barber->execute();

    header("Location: edit_services.php"); // รีเฟรชหน้าเพื่อแสดงการเปลี่ยนแปลง
    exit();
}

// การเพิ่มหรือแก้ไขบริการ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];
    $type_id = $_POST['type_id'];

    if (isset($_POST['service_id'])) {
        // การแก้ไขบริการ
        $service_id = $_POST['service_id'];
        $update_sql = "UPDATE services SET service_name = ?, service_description = ?, price = ?, type_id = ? WHERE service_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssdii", $service_name, $service_description, $price, $type_id, $service_id);
        $stmt_update->execute();
    } else {
        // การเพิ่มบริการใหม่
        $insert_sql = "INSERT INTO services (service_name, service_description, price, type_id) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ssdi", $service_name, $service_description, $price, $type_id);
        $stmt_insert->execute();
    }

    header("Location: edit_services.php"); // รีเฟรชหน้าเพื่อแสดงการเปลี่ยนแปลง
    exit();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบริการ</title>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJx3PQ2nFvP2d1m9lZGxQqH6tQddZ5gkL3RoDgUzF+6yP5vF5H1Rexh4dO2y" crossorigin="anonymous">
    <style>
        .container {
            margin-top: 50px;
        }
        h1 {
            text-align: center;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
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
    <div class="heading_container heading_center">
        <h1>Manage services</h1>
    </div>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Service Name</th>
                <th>Price</th>
                <th>Type Service</th>
                <th>EDIT</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_services->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['service_id']; ?></td>
                    <td><?php echo $row['service_name']; ?></td>
                    <td><?php echo $row['price']; ?> บาท</td>
                    <td>
                        <?php
                        // ดึงประเภทบริการ
                        $sql_type = "SELECT type_name FROM service_types WHERE type_id = ?";
                        $stmt_type = $conn->prepare($sql_type);
                        $stmt_type->bind_param("i", $row['type_id']);
                        $stmt_type->execute();
                        $result_type = $stmt_type->get_result();
                        $type = $result_type->fetch_assoc();
                        echo $type['type_name'];
                        ?>
                    </td>
                    <td>
                        <a href="edit_services.php?service_id=<?php echo $row['service_id']; ?>" class="btn btn-warning btn-sm">EDIT</a>
                        <a href="edit_services.php?delete_service=<?php echo $row['service_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบบริการนี้จริงหรือ?')">DELETE</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybSnQbTjmS6cFC7p4jAzDPLGhG4mQZV0QXZv7WrPyy8Pn1V0x" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0jjj5H1a1XQzQvg/cz5Tp1tF5Ff0JzZ9Pp4T6JX3ZIQlffp2" crossorigin="anonymous"></script>

</body>
</html>
