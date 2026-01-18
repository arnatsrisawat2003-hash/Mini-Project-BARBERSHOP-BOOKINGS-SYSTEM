<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
include('database.php'); // เชื่อมต่อกับไฟล์ฐานข้อมูล

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

// ตรวจสอบว่าได้รับ `service_id` จาก URL หรือไม่
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // ดึงข้อมูลบริการจากฐานข้อมูล
    $sql = "SELECT * FROM services WHERE service_id = '$service_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        echo "ไม่พบบริการนี้ในระบบ";
        exit();
    }
} else {
    echo "ไม่พบรหัสบริการ";
    exit();
}

// ถ้ากดปุ่ม "บันทึกการเปลี่ยนแปลง"
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];
    $type_id = $_POST['type_id'];

    $sql_update = "UPDATE services 
                  SET service_name = '$service_name', service_description = '$service_description', 
                      price = '$price', type_id = '$type_id' 
                  WHERE service_id = '$service_id'";

    if ($conn->query($sql_update) === TRUE) {
        $success_message = "✅ บริการได้รับการอัปเดตเรียบร้อยแล้ว!";
    } else {
        $error_message = "❌ เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงข้อมูลประเภทบริการ (service types) เพื่อแสดงใน dropdown
$sql_types = "SELECT * FROM service_types";
$result_types = $conn->query($sql_types);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขบริการ</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />
    <style>
        .container{
          font-family: "Bebas Neue", sans-serif;
        }
    </style>
</head>
<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
    <div class="hero_bg_box">
      <div class="img-box">
        <img src="images/hero-bg.jpg" alt="">
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
    <div class="heading_container heading_center">
        <h2>EDIT Services</h2>
    </div>
</section>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Edit Services</h4>
        </div>
        <div class="card-body">
        <?php if (!empty($success_message)) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="service_name" class="form-label">Services Name</label>
                    <input type="text" name="service_name" class="form-control" 
                           value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="service_description" class="form-label">Description</label>
                    <textarea name="service_description" class="form-control" rows="3"><?php echo htmlspecialchars($service['service_description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price (บาท)</label>
                    <input type="number" name="price" class="form-control" 
                           value="<?php echo $service['price']; ?>" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="type_id" class="form-label">Service Time</label>
                    <select name="type_id" class="form-select" required>
                        <?php
                        if ($result_types->num_rows > 0) {
                            while($row = $result_types->fetch_assoc()) {
                                $selected = ($row['type_id'] == $service['type_id']) ? 'selected' : '';
                                echo "<option value='" . $row['type_id'] . "' $selected>" . $row['type_name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>ไม่มีประเภทบริการ</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="view_services.php" class="btn btn-secondary"> Back</a>
                    <button type="submit" class="btn btn-success">Save changes.</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
