<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'barber') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['customer_id'])) {
    echo "ไม่พบข้อมูลลูกค้า";
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

$customer_id = intval($_GET['customer_id']);

// ดึงข้อมูลลูกค้า
$sql_customer_info = "SELECT name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_customer_info);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result_customer = $stmt->get_result();

if ($result_customer->num_rows > 0) {
    $customer = $result_customer->fetch_assoc();
} else {
    echo "ไม่พบข้อมูลลูกค้าดังกล่าว";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ข้อมูลลูกค้า</title>
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
    @import url('https://fonts.googleapis.com/css?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap');
    body {
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    .container_LAZ {
      font-family: "Bebas Neue", sans-serif;
      max-width: 500px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .profile-section {
      margin-top: 20px;
    }

    .section-title {
      color: black;
      padding: 10px;
      margin: 0;
      font-size: 2rem;
    }

    .profile-details {
      background-color: #fff;
      color: black;
      padding: 15px;
      line-height: 1.8;
      font-size: 1.2rem;
    }

    .back-btn {
      background-color: #000;
      border: 0.5px solid black;
      color: #fff;
      padding: 10px 30px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
      margin-top: 20px;
    }

    .back-btn:hover {
      background-color: white;
      color: black;
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
              <span>BBshop</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>
            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav">
                <li class="nav-item active">
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
            Customer Info
        </h2>
      </div>
    </div>
  </section>

<div class="container_LAZ">
  <div class="profile-section">
    <h2 class="section-title">Personal Information</h2>
    <div class="profile-details">
      <p>NAME: <?php echo htmlspecialchars($customer['name']); ?></p>
      <p>EMAIL: <?php echo htmlspecialchars($customer['email']); ?></p>
      <p>PHONE NUMBER: <?php echo htmlspecialchars($customer['phone_number']); ?></p>
    </div>
  </div>

  <a href="barber_dashboard.php" class="back-btn">Back</a>
</div>

</body>
</html>
