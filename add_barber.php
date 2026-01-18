<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('database.php');
$message = "";


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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $services = $_POST['services'] ?? [];
    
    // ตรวจสอบข้อมูล
    if (empty($username) || empty($password) || empty($name) || empty($email) || empty($phone_number)) {
        $message = "❌ กรุณากรอกข้อมูลให้ครบทุกช่อง!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ อีเมลไม่ถูกต้อง!";
    } else {
        // ตรวจสอบว่า username หรือ email ซ้ำหรือไม่
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "❌ ชื่อนี้หรืออีเมลนี้ถูกใช้ไปแล้ว!";
        } else {
            // การแฮชรหัสผ่าน
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // การอัปโหลดไฟล์รูปภาพ
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $target_dir = "img/profiles";
                $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
                // ตรวจสอบชนิดของไฟล์
                $allowed_types = array("jpg", "jpeg", "png", "gif");
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                        $profile_image = basename($_FILES["profile_image"]["name"]);
                    } else {
                        $message = "❌ เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพ";
                    }
                } else {
                    $message = "❌ ไฟล์ภาพต้องเป็น .jpg, .jpeg, .png หรือ .gif เท่านั้น";
                }
            }

            if (empty($message)) {
                // เพิ่มข้อมูลลงในฐานข้อมูล
                $stmt = $conn->prepare("INSERT INTO users (username, password, role, name, email, phone_number, profile_image) VALUES (?, ?, 'barber', ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $username, $hashed_password, $name, $email, $phone_number, $profile_image);
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;

                    foreach ($services as $service_id) {
                        $stmt_service = $conn->prepare("INSERT INTO barber_services (user_id, service_id) VALUES (?, ?)");
                        $stmt_service->bind_param("ii", $user_id, $service_id);
                        $stmt_service->execute();
                        $stmt_service->close();
                    }
                    echo "<script>alert('✅ เพิ่มช่างสำเร็จ!');</script>";
                } else {
                    echo "<script>alert('❌ เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
                }
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Au Register Forms by Colorlib</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
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

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
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
    <div class="page-wrapper  p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">Add Barber User</h2>
                    <?php if (!empty($message)) { ?>
                        <div class="alert <?php echo (strpos($message, '✅') !== false) ? 'success' : ''; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php } ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="input-group">
                            <input class="input--style-1" type="text" placeholder="USERNAME" name="username" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="text" placeholder="NAME" name="name" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="email" placeholder="EMAIL" name="email" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="password" placeholder="PASSWORD" name="password" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="tel" placeholder="PHONE" name="phone_number" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="file" placeholder="PROFILE" name="profile_image" required>
                        </div>
                            <button class="btn btn--radius btn--green" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
